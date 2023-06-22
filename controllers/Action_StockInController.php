<?php

use Models\Category;
use Models\Item;
use Models\Stock;
use Models\UnitMeasure;

@session_start();

require_once("rootcwd.inc.php");

global $rootCwd;

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/Category.php");
require_once($rootCwd . "models/UnitMeasure.php");
require_once($rootCwd . "models/Stock.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());
$security->BlockNonPostRequest();

// Record key is the encrypted record id
$recordKey = $_POST['item-key'] ?? "";

// if (isset($_SESSION['stockin-item-key']))
// {
//     $recordKey = $_SESSION['stockin-item-key'];
//     unset($_SESSION['stockin-item-key']);
// }
// Make sure that the record key is present. Exit if none
if (empty($recordKey))
{
    Response::Redirect(Pages::STOCK_IN, 403);
    exit;
}

$db     = new DbHelper($pdo);
$items  = TableNames::inventory;
$categories = TableNames::categories;
$units = TableNames::unit_measures;

$i      = Item::getFields();
$c      = Category::getFields();
$u      = UnitMeasure::getFields();
$s      = Stock::getFields(); 

try
{ 
    // Decrypt the record id
    $recordId = $security->Decrypt($recordKey);

    $stmt_itemDetails = $db->getInstance()->prepare
    (
        "SELECT 
            i.$i->itemName      AS 'item',
            c.$c->name          AS 'category',
            i.$i->itemImage     AS 'display',
            i.$i->criticalLevel AS 'restockPoint',
            i.$i->remaining     AS 'available',
            u.$u->measurement   AS 'units'

        FROM $items AS i
        LEFT JOIN $categories AS c ON c.$c->id = i.$i->category	
        LEFT JOIN $units AS u ON u.$u->id = i.$i->unitMeasure
        WHERE i.$i->id = ?"
    );

    $stmt_itemDetails->execute([$recordId]);

    $itemDetails = $stmt_itemDetails->fetch(PDO::FETCH_ASSOC) ?? [];

    if (empty($itemDetails))
    {
        IError::Throw(500);
        exit;
    }

    // Stock on hand
    $stockOnHand = $db->select(TableNames::stock, 
    [
        $s->sku, 
        $s->quantity,
        $s->expiry_date,
        $s->dateCreated
    ], 
    [ $s->item_id => $recordId ], 
    $db->ORDER_MODE_DESC, $s->dateCreated); 
}
catch (Exception $ex)
{
    echo $ex->getMessage(); exit;
    IError::Throw(500);
    exit;
}

//---------------------------------------------
// ITEM INFORMATION
//---------------------------------------------
function getPreviewImage()
{
    global $itemDetails;

    return ENV_SITE_ROOT . "storage/uploads/items/" . $itemDetails['display'];
}

function getItemName()
{
    global $itemDetails;

    return $itemDetails['item'];
}

function getCategory()
{
    global $itemDetails;

    return $itemDetails['category'];
}

function getRemaining()
{
    global $itemDetails;

    $color = "bg-teal text-white";
    $remaining = $itemDetails['available'];

    if ($remaining <= $itemDetails['restockPoint'] && $remaining > 0)
        $color = "bg-amber-300 font-base";

    if ($remaining == 0)
        $color = "bg-red text-white";

    $content = $remaining." ".$itemDetails['units'];

    $h6 = <<<H6
    <h6 class="text-wrap $color px-2 py-1 rounded-3 fw-normal flex-fill">$content</h6>
    H6;

    return $h6;
}

function getRestockPoint()
{
    global $itemDetails;

    return $itemDetails['restockPoint']." ".$itemDetails['units'];
}

function getErrorMessage()
{
    $message = "";

    if (isset($_SESSION['stockin-action-error']))
    {
        $message = $_SESSION['stockin-action-error'];
        unset($_SESSION['stockin-action-error']);
    }

    echo $message;
}

function getItemKey()
{
    global $recordKey;
    return $recordKey;
}

function getStockOnHand()
{
    global $stockOnHand, $s, $itemDetails;

    if (!empty($stockOnHand))
    {
        $units = $itemDetails['units'];

        foreach ($stockOnHand as $obj)
        {
            $sku = $obj[$s->sku];
            $qty = $obj[$s->quantity]." ".$units;

            $exp = "No expiry";
            
            if (!empty($obj[$s->expiry_date]))
                $exp = Dates::toString($obj[$s->expiry_date], "M. d, Y");

            $bestBefore = "<div class=\"text-muted fsz-12\">$exp</div>";

            if (!empty($obj[$s->expiry_date]) && Dates::isPast($exp))
            { 
                $bestBefore = "<div class=\"font-red fsz-12\">Expired</div>";
            }
            
            echo <<<TR
            <tr class="align-middle">
                <td class="text-truncate ">
                    <div class="text-primary fw-bold fsz-12 text-uppercase">$sku</div>
                    <div class="text-muted fsz-12">Expiry Date:</div>
                </td>
                <td class="text-truncate">
                    <div class="d-flex flex-row">
                        <div class="ms-auto flex-fill flex-column text-wrap d-flex text-truncate">
                            <div class="font-base fw-bold td-item-name text-truncate">$qty</div>
                            $bestBefore
                        </div>
                    </div>
                </td>
            </tr>
            TR;
        }
    }
}