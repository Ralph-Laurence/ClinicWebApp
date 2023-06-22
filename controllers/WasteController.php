<?php

use Models\Item;
use Models\Waste; 

@session_start();

require_once("rootcwd.inc.php");

global $rootCwd;

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "models/Waste.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");
  
require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());

$db             = new DbHelper($pdo); 
$itemFields     = Item::getFields();
$wasteFields    = Waste::getFields();
$waste          = new Waste($db); 
$totalRecords   = 0;
$recordYear = 'Unknown';

try
{ 
    $dataset = $waste->showAll();
    $totalRecords = count($dataset);
    $recordYear = $waste->getRecordYear();
  
    // For waste record truncate 
    $truncKey = IString::random(8);
    $_SESSION['waste-truncate-key'] = $truncKey;
}
catch (Exception $ex)
{  
    IError::Throw(500);
    exit;
}

// Bind the dataset's rows into the UI table
function bindDataset()
{   
    global $dataset, $security, $wasteFields;

    if (empty($dataset))
        return;

    $filter = getFilter()['filter'];
    $query  = getFilter()['query'];

    foreach ($dataset as $obj) 
    {   
        // Filter result sets by Disposal Reason
        $reason = $obj[$wasteFields->reason];

        if ($filter == "reas")
        {
            $reasonType = $security->Decrypt($query);

            if ($reason != Item::getStockoutReason($reasonType))
                continue;
        }

        // Filter result sets by item category
        $categoryId = $obj['categoryID'];

        if ($filter == "catg")
        {
            $catgId = $security->Decrypt($query);

            if ($categoryId != $catgId)
                continue;
        }

        $dateCreated    = Dates::toString($obj[$wasteFields->dateCreated], "M. d, Y");
        $image          = $obj['image'];
        $item           = $obj['item'];
        $code           = $obj['code'];
        $category       = $obj['category'];
        $icon           = $obj['icon']; 
        $sku            = $obj['sku'];

        $id     = $security->Encrypt($obj[$wasteFields->id]);
        $amount = $obj[$wasteFields->amount] ." ". $obj['units'];
   
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
            <td class="px-2 text-center mx-0 row-check-parent">
                <div class="d-inline">
                    <input class="form-check-input px-0 mx-0" type="checkbox" id="row-check-box" value="" />
                </div>
            </td>
            <td class="text-primary fw-bold fsz-12 text-uppercase text-truncate th-150 td-reason">$reason</td>
            <td class="th-200 text-truncate px-1">
                <div class="d-flex align-items-center">
                    <div class="ms-0">
                        <img src="$icon" class="me-3 item-icon" alt="icon" width="32" height="32">
                    </div>
                    <div class="ms-auto flex-fill flex-column text-wrap d-flex text-truncate">
                        <div class="fs-6 font-base fw-bold td-item-name text-truncate">$item</div>
                        <div class="fsz-12 text-muted fst-italic text-truncate text-uppercase td-category">$category</div>
                        <div class="fsz-12 font-teal fst-italic text-truncate text-uppercase td-item-code">$sku</div>
                    </div>
                </div>
            </td>
            <td class="text-truncate th-150 td-dispose-amount">$amount</td>
            <td class="text-truncate th-100 px-1 td-date-disposed">$dateCreated</td>
            <td class="text-center th-75">
                <button type="button" class="btn btn-secondary fw-bold py-1 px-2 trash-btn">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
            <td class="d-none">
                <input type="text" class="item-key" value="$id" />
                <input type="text" class="reserve-stock" value="reserve" />
            </td> 
            <td class="d-none">$item</td> 
            <td class="d-none">$code</td> 
        </tr>
        TR;
    } 
}
 

function getErrorMessage()
{
    $message = "";

    if (isset($_SESSION['waste-action-error']))
    {
        $message = $_SESSION['waste-action-error'];
        unset($_SESSION['waste-action-error']);
    }

    echo $message;
}

function getSuccessMessage()
{
    $message = "";

    if (isset($_SESSION['waste-action-success']))
    {
        $message = $_SESSION['waste-action-success'];
        unset($_SESSION['waste-action-success']);
    }

    echo $message;
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

function createFilterBadge()
{
    $filter = getFilter()['filter'];
 
    $filters = 
    [
        'catg' => "Category",
        'reas' => "Disposal Reason"
    ];
 
    if (!empty($filter) && array_key_exists($filter, $filters))
    {
        $label = $filters[$filter];

        echo <<<BADGE
        <div class="capsule-badge fsz-14 text-white display-none effect-reciever" data-transition-index="3" data-transition="fadein">
            <div class="d-flex align-items-center">
                <div class="capsule-badge-bg rounded-start px-2">
                    <i class="fas fa-filter fsz-10 me-2"></i>Filter
                </div>
                <div class="bg-mdb-vivid-purple rounded-end px-2 capsule-badge-indicator">
                    $label
                </div>
            </div> 
        </div>
        BADGE;
    }
}

function createFilterItems()
{   
    // Filter Item Data
    $data = 
    [
        [ "label"  => "Show All",        'filter' => '',        "href" => Pages::WASTE, "action" => "" ],
        [ "label"  => 'By Category',     'filter' => 'catg',    "href" => "#",          "action" => "data-mdb-target='#findCategoryModal' data-mdb-toggle='modal'" ],
        [ "label"  => "Disposal Reason", 'filter' => 'reas',    "href" => "#",          "action" => "data-mdb-target='#findReasonModal' data-mdb-toggle='modal'"]//"onclick=\"setfilteraction('reason')\"" ],
    ];
 
    foreach($data as $filterItem)
    { 
        $icon   = (getFilter()['filter'] == $filterItem["filter"]) ? "selected" : "search";
        $liAction = $filterItem["action"]; 

        echo <<<LI
        <li>
            <a href="{$filterItem["href"]}" role="button" class="d-flex align-items-center gap-3 px-3 py-1 dropdown-item-custom-light li-user-filter" $liAction>
                <div class="dropdown-item-icon text-center"></div>
                <div class="d-flex align-items-center gap-2">
                    <img src="assets/images/icons/filter-$icon.png" width="18" height="18">
                    <div class="fs-6">{$filterItem["label"]}</div>
                </div>
            </a>
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

function getTruncateKey()
{
    global $security;

    $key = "";

    if (isset($_SESSION['waste-truncate-key']))
        $key = $security->Encrypt( $_SESSION['waste-truncate-key'] );

    return $key;
}