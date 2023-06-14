<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");

require_once($rootCwd . "models/Supplier.php");
require_once($rootCwd . "models/Item.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

use Models\Item;
use Models\Supplier; 

$security   = new Security();
 
$security->requirePermission(Chmod::PK_SUPPLIERS, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_SUPPLIERS, UserAuth::getId());
$security->BlockNonPostRequest();

$db   = new DbHelper($pdo); 
$supplier = new Supplier($db);
$supplierFields = Supplier::getFields();

$detailsKey = $_POST['details-key'] ?? "";
 
if (empty($detailsKey))
{
    Response::Redirect(Pages::SUPPLIERS, 301);
}
 
try 
{
    // Details key is an encrypted Record Id. We must decrypt it first
    $recordId = $security->Decrypt($detailsKey);

    // Load the supplier info from database
    $dataset = $supplier->find($recordId);

    // If there was no data found, exit the execution
    if (empty($dataset)) {
        onError();
    } 

    // Get the medical supplies that are supplied by supplier
    $medSupplies = $supplier->getMedicalSupplies($recordId);
} 
catch (\Exception $ex) { echo $ex->getMessage(); exit; onError(); }
catch (\Throwable $ex) { echo $ex->getMessage(); exit; onError(); }
 
function onError()
{
    IError::Throw(500);
    exit;
}
 
function getName()
{
    global $dataset, $supplierFields;

    return $dataset[$supplierFields->name];
}

function getAddress()
{
    global $dataset, $supplierFields;

    return $dataset[$supplierFields->address];
}

function getEmail()
{
    global $dataset, $supplierFields;

    return $dataset[$supplierFields->email];
}

function getContact()
{
    global $dataset, $supplierFields;

    return $dataset[$supplierFields->contact];
}

function getDesc()
{
    global $dataset, $supplierFields;

    return $dataset[$supplierFields->description];
}

function bindMedicalSupplies()
{
    global $medSupplies, $security;
    $fields = Item::getFields();

    if (empty($medSupplies))
    {
        echo <<<DIV
        <div class="p-2">
            <div class="bg-amber-light p-2 fst-italic rounded-2">No medical supplies to show.</div>
        </div>
        DIV;
    }

    foreach ($medSupplies as $obj)
    {
        $itemName = $obj[$fields->itemName];
        $itemCode = $obj[$fields->itemCode];
        $category = $obj['category'];
        $id = $security->Encrypt($obj[$fields->id]);

        echo <<<TR
        <tr class="align-middle">
            <td class="text-truncate th-230">
                <div class="text-truncate fw-bold text-primary text-uppercase fsz-14 mb-2">$itemName</div>
                <div class="text-truncate fw-bold font-teal text-uppercase fsz-10">$itemCode</div>
                <div class="text-truncate fw-bold text-uppercase fsz-10">$category</div>
            </td>
            <td class="th-75 text-center">
                <button type="button" class="btn btn-primary btn-details px-2 py-1 text-center">Details</button>
            </td>
            <td class="d-none">
                <input type="text" class="record-key" value="$id" />
            </td>
        </tr>
        TR;
    } 
}
 