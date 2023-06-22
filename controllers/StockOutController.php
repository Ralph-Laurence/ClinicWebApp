<?php
@session_start();

require_once("rootcwd.inc.php");

global $rootCwd;

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "models/Item.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());

use Models\Item;

$db = new DbHelper($pdo);
$items = new Item($db);
$itemFields = Item::getFields();

$totalSoldout = 0;
$totalCritical = 0;
$totalExpired = 0;
$totalMedicines = 0;

try
{ 
    $dataset = $items->showAll();
    $totalMedicines   = count($dataset);
}
catch (Exception $ex)
{
    IError::Throw(500);
    exit;
}

// Bind the dataset's rows into the UI table
function bindDataset()
{  
    global $dataset, $itemFields, $security, $totalCritical, $totalSoldout, $totalExpired;

    if (empty($dataset))
        return;
    
    $filter = getFilter()['filter'];
    $query  = getFilter()['query'];
    
    $collectExpiredIds = [];

    foreach ($dataset as $obj) 
    {  
        $critical   = $obj[$itemFields->criticalLevel];
        $stock      = $obj[$itemFields->remaining];

        if ($filter == "s0" && $stock > 0)
            continue;

        // Expired
        if ($filter == 'x')
        {
            if (empty($obj['expiryDate']))
                continue;

            $isExpired = (strtotime(date('Y-m-d')) > strtotime($obj['expiryDate']));

            if ($isExpired && $stock == 0)
                continue;

            if (!$isExpired)
                continue;
        }

        if ($filter == "s1" && ($stock > $critical || $stock <= 0))
            continue;

        if (!empty($query))
        {
            $categoryId = $security->Decrypt($query); 

            if ($obj['categoryID'] != $categoryId)
                continue;
        }

        //$stock      = $obj[$itemFields->remaining];
        $units      = $obj['units'];
        //$critical   = $obj[$itemFields->criticalLevel];
        $stockText  = "$stock $units";
        $itemId     = $security->Encrypt($obj[$itemFields->id]);
        $reserve    = "$critical $units";

        $itemCode = $obj[$itemFields->itemCode];
        $itemName = $obj[$itemFields->itemName];
        $category = $obj['category'];
        
        $stockLabel = $stockText;
 
        $actionButton = <<< BTN
        <button type="button" class="btn shadow-1-soft btn-light-red fw-bold py-1 px-2 i-stockout-btn">Stock Out</button>
        BTN;
        // $actionButton = <<< BTN
        // <button type="button" class="btn shadow-1-soft btn-light-red fw-bold py-1 px-2 action-btn">Stock Out</button>
        // BTN;
 
        // Sold out 
        if ($stock <= 0)
        { 
            $stockLabel = "<div class=\"stock-label stock-label-soldout\">Out of Stock</div>";
            $totalSoldout++;

            $actionButton = ""; // Hide the stockout button because there is nothing to pullout
        }

        // Critical
        if ($stock <= $critical && $stock > 0)
        { 
            $stockLabel = "<div class=\"stock-label stock-label-critical\">$stock $units</div>";
            $totalCritical++;
        }

        // Expired
        if (Dates::isPast($obj['expiryDate']))
        { 
            if ($stock > 0)
            {
                $stockLabel = <<<DIV
                <div class="stock-label stock-label-expired" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="top" title="<span class='tooltip-title tooltip-title-amber'>Expired</span><br>$stock $units are no longer safe to use and must be discarded.">
                    <i class="fas fa-info-circle me-1"></i>
                    Expired
                    <span class="expired-stock-units d-none">$stock $units</span>
                </div>
                DIV;

                $actionButton = <<<BTN
                <button type="button" class="btn btn-secondary bg-red text-white fw-bold py-1 px-2 discard-btn">Discard</button>
                BTN; 

                $totalExpired++;
            } 
            else if ($stock == 0)
            {
                $collectExpiredIds[] = $obj[$itemFields->id];
            }
        }
        
        $image = $obj[$itemFields->itemImage];
        $icon  = "assets/images/inventory/" . $obj['icon'] . ".png";

        // If image is not empty, use it
        if (!empty($image))
            $icon = UPLOADS_URL . "items/$image";

        // Otherwise, use an icon
        else {
            if (empty($obj['icon']))
                $icon = "assets/images/icons/icn_no_image.png";
        } 

        echo <<<TR
        <tr class="align-middle">
            <td class="text-primary fw-bold fsz-12 text-truncate th-150">$itemCode</td>
            <td class="th-280 text-truncate">
                <div class="d-flex align-items-center">
                    <div class="ms-0">
                        <img src="$icon" class="me-3 item-icon" alt="icon" width="32" height="32">
                    </div>
                    <div class="ms-auto flex-fill flex-column text-wrap d-flex text-truncate">
                        <div class="font-base fw-bold td-item-name text-truncate">$itemName</div>
                        <div class="fsz-12 text-muted fst-italic text-truncate text-uppercase">$category</div>
                    </div>
                </div>
            </td>
            <td class="text-truncate th-150 td-current-stock">$stockLabel</td>
            <td class="text-truncate th-100">$critical</td>
            <td class="text-center th-100">
                $actionButton
            </td>
            <td class="d-none">
                <input type="text" class="item-key" value="$itemId" />
                <input type="text" class="reserve-stock" value="$reserve" />
                <input type="text" class="td-max-qty" value="$stock" />
            </td> 
        </tr>
        TR;
    }

    if (!empty($collectExpiredIds))
    {
        $itemFields->resetExpiryDates($collectExpiredIds);
    }
}

function includeRestockModal($action)
{
    global $rootCwd;

    $mode = $action;    // 0 -> Stock In, 1 -> Stock Out 
    require_once($rootCwd . "includes/embed.restock-modal.php");
}

function getCountersData()
{
    global $totalCritical, $totalSoldout, $totalExpired;

    return json_encode([
        'totalCritical' => $totalCritical,
        'totalSoldout'  => $totalSoldout,
        'totalExpired'  => $totalExpired
    ]);
}

function getSuccessMessage()
{
    $message = "";

    if (isset($_SESSION['stockout-action-success']))
    {
        $message = $_SESSION['stockout-action-success'];
        unset($_SESSION['stockout-action-success']);
    }

    echo $message;
}

function setSenderKey()
{
    global $security;
    return $security->Encrypt("2");
}

function generateStockoutReasons()
{
    global $security;

    foreach (Item::StockoutReasons as $k => $obj)
    {
        $key = $security->Encrypt($k);

        echo <<< LI
        <li class="dropdown-item" onclick="setStockoutReason(this, '$key')"
        data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="right" title="<span class='tooltip-title tooltip-title-amber'>{$obj['reason']}</span><br>{$obj['description']}">
            <div class="flex-row d-flex align-items-center gap-3">
                <span class="li-label flex-fill">{$obj['reason']}</span>
                <i class="fas fa-info-circle me-0"></i>
            </div>
        </li> 
        LI;
    }
}

function getFilter()
{  
    return [
        'filter' => $_GET['filter'] ?? '', // filter key
        'query'  => $_GET['query']  ?? ''  // filter value
    ];
}

function createFilterItems()
{   
    // Filter Item Data
    $data = 
    [
        [ "label"  => "Show All",       'filter' => '',      "action" => "onclick=\"setfilteraction('*')\"" ],
        [ "label"  => 'By Category',    'filter' => 'catg',  "action" => "data-mdb-target='#findCategoryModal' data-mdb-toggle='modal'" ],
        [ "label"  => "Critical Stock", 'filter' => 's1',    "action" => "onclick=\"setfilteraction('s1')\"" ],
        [ "label"  => "Out of Stock",   'filter' => 's0',    "action" => "onclick=\"setfilteraction('s0')\"" ],
        [ "label"  => "Expired",        'filter' => 'x',     "action" => "onclick=\"setfilteraction('x')\"" ],
    ];
 
    foreach($data as $filterItem)
    { 
        $icon   = (getFilter()['filter'] == $filterItem["filter"]) ? "selected" : "search";
        $liAction = $filterItem["action"]; 

        echo <<<LI
        <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light li-user-filter" $liAction>
            <div class="dropdown-item-icon text-center"></div>
            <div class="d-flex align-items-center gap-2">
                <img src="assets/images/icons/filter-$icon.png" width="18" height="18">
                <div class="fs-6">{$filterItem["label"]}</div>
            </div>
        </li>
        LI;
    }
}

function createFilterBadge()
{
    $filter = getFilter();

    $filters = 
    [
        'catg' => "Category",
        's0'   => 'Out of Stock',
        's1'   => 'Critical Stock',
        'x'    => 'Expired'
    ];

    $filterKey = getFilter()['filter'];

    if (!empty($filter) && array_key_exists($filterKey, $filters))
    {
        $label = $filters[$filterKey];

        echo <<<BADGE
        <div class="capsule-badge fsz-14 text-white display-none effect-reciever" data-transition-index="5" data-transition="fadein">
            <div class="d-flex align-items-center">
                <div class="capsule-badge-bg rounded-start px-2">
                    <i class="fas fa-filter fsz-10 me-2"></i>Filter
                </div>
                <div class="bg-mdb-purple rounded-end px-2 capsule-badge-indicator">
                    $label
                </div>
            </div> 
        </div>
        BADGE;
    }
}
