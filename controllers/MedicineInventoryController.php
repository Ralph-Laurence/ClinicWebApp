<?php
 
@session_start();

require_once("rootcwd.php");
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");  
require_once($rootCwd . "library/semiorbit-guid.php");  

require_once($rootCwd . "models/Item.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php"); 

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

use Models\Item; 

$security   = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());

$db         = new DbHelper($pdo);
$items      = new Item($db);
$itemFields = Item::getFields(); 
 
$totalMedicines = 0;
$totalExpired   = 0;
$totalCritical  = 0;
$totalSoldout   = 0;

try 
{
    $dataset = $items->showAll();
 
    $totalMedicines = count($dataset);
} 
catch (\Exception $ex) { onError(); }
catch (\Throwable $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['items-actions-success-msg']))
    {
        $msg = $_SESSION['items-actions-success-msg'];
        unset($_SESSION['items-actions-success-msg']);
    }

    return $msg;
}

function getErrorMessage()
{
    $msg = "";

    if (isset($_SESSION['items-actions-error-msg']))
    {
        $msg = $_SESSION['items-actions-error-msg'];
        unset($_SESSION['items-actions-error-msg']);
    }

    return $msg;
}

function bindDataset()
{
    global $dataset, $itemFields, $security, $items, $totalCritical, $totalSoldout, $totalExpired;

    if (empty($dataset))
        return;
 
    $filter = getFilter()['filter'];
    $query  = getFilter()['query'];

    $collectExpiredIds = [];
    
    foreach ($dataset as $obj) 
    {
        $critical  = $obj[$itemFields->criticalLevel];
        $remaining = $obj[$itemFields->remaining];

        if ($filter == "s0" && $remaining > 0)
            continue;

        // Expired
        if ($filter == 'x')
        {
            if (empty($obj['expiryDate']))
                continue;

            $isExpired = (strtotime(date('Y-m-d')) > strtotime($obj['expiryDate']));

            if ($isExpired && $remaining == 0)
                continue;

            if (!$isExpired)
                continue;
        }

        if ($filter == "s1" && ($remaining > $critical || $remaining <= 0))
            continue;

        if (!empty($query))
        {
            $categoryId = $security->Decrypt($query); 

            if ($obj['categoryID'] != $categoryId)
                continue;
        }

        $id = $security->Encrypt($obj[$itemFields->id]);
        $itemCode = $obj[$itemFields->itemCode];
        $itemName = $obj[$itemFields->itemName];
        $category = $obj['category'];

        if (empty($category))
            $category = "Uncategorized";
 
        $stock = $remaining ." ". $obj['units'];
        $stockLabel = 
        "<div class=\"text-truncate\">
            <span class=\"font-green\">&#x25cf;</span>
            $stock
        </div>";

       // Sold out 
        if ($remaining <= 0)
        { 
            $stockLabel = "<div class=\"stock-label stock-label-soldout\">Out of Stock</div>";
            $totalSoldout++;
        }

        // Critical
        if ($remaining <= $critical && $remaining > 0)
        { 
            $stockLabel = "<div class=\"stock-label stock-label-critical\">$stock</div>";
            $totalCritical++;
        }

        // Expired
        if (Dates::isPast($obj['expiryDate']))
        { 
            if ($remaining > 0)
            {
                $stockLabel = <<<DIV
                <div class="stock-label stock-label-expired" data-mdb-toggle="tooltip" data-mdb-html="true" data-mdb-placement="top" title="<span class='tooltip-title tooltip-title-amber'>Expired</span><br>$stock are no longer safe to use and must be discarded.">
                    <i class="fas fa-info-circle me-1"></i>
                    Expired
                </div>
                DIV;

                $totalExpired++;
            } 
            else if ($remaining == 0)
            {
                $collectExpiredIds[] = $obj[$itemFields->id];
            }
        }
 
        $image = $obj[$itemFields->itemImage];
        $icon  = "assets/images/inventory/". $obj['icon'] .".png";

        // If image is not empty, use it
        if (!empty( $image ))
            $icon = UPLOADS_URL . "items/$image";
        
        // Otherwise, use an icon
        else
        {
            if (empty($obj['icon']))
                $icon = "assets/images/icons/icn_no_image.png";
        }
        
        echo <<<TR
        <tr class="align-middle tr-inventory">
            <td class="px-2 text-center mx-0 row-check-parent">
                <div class="d-inline">
                    <input class="form-check-input px-0 mx-0" type="checkbox" id="row-check-box" value="" />
                </div>
            </td>
            <td class="th-150 text-primary fw-bold text-truncate text-uppercase fsz-12">$itemCode</td>
            <td class="th-280 text-truncate">
                <div class="d-flex align-items-center">
                    <div class="ms-0">
                        <img src="$icon" class="me-3 restock-item-icon" alt="icon" width="32" height="32">
                    </div>
                    <div class="ms-auto flex-fill flex-column text-wrap d-flex text-truncate">
                        <div class=" font-base fw-bold td-item-name text-truncate">$itemName</div>
                        <div class="fsz-12 text-muted fst-italic text-truncate text-uppercase">$category</div>
                    </div>
                </div>
            </td>
            <td class="th-180">$stockLabel</td>
            <td class="th-100 text-center">$critical</td>
            <td class="text-center px-1 th-100">
                <div class="btn-group">
                    <button type="button" class="btn btn-primary btn-details px-2 py-1 text-center">Details</button>
                    <button type="button" class="btn btn-primary btn-split-arrow px-0 py-1 text-center dropdown-toggle dropdown-toggle-split" data-mdb-toggle="dropdown" aria-expanded="false"></button>
                    <ul class="dropdown-menu shadow-2-strong dropdown-menu-custom-light-small">
                        <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light tr-action-edit">
                            <div class="dropdown-item-icon text-center">
                                <i class="fas fa-pen fs-6 text-warning"></i>
                            </div>
                            <div class="fs-6">Edit</div>
                        </li>
                        <li class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light tr-action-delete">
                            <div class="dropdown-item-icon text-center">
                                <i class="fas fa-trash fs-6 font-red"></i>
                            </div>
                            <div class="fs-6">Delete</div>
                        </li>
                    </ul>
                </div>
            </td>
            <td class="d-none">
                <input type="text" class="record-key" value="$id" />
            </td>
            <td class="d-none">$itemName</td>
        </tr>
        TR;
    }

    if (!empty($collectExpiredIds))
    {
        $items->resetExpiryDates($collectExpiredIds);
    }
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

function getFilter()
{  
    return [
        'filter' => $_GET['filter'] ?? '', // filter key
        'query'  => $_GET['query']  ?? ''  // filter value
    ];
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

function getCountersData()
{
    global $totalCritical, $totalSoldout, $totalExpired;

    return json_encode([
        'totalCritical' => $totalCritical,
        'totalSoldout'  => $totalSoldout,
        'totalExpired'  => $totalExpired
    ]);
}