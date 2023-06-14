<?php
@session_start();

require_once("rootcwd.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php"); 
require_once($rootCwd . "includes/urls.php"); 

require_once($rootCwd . "library/semiorbit-guid.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");

require_once($rootCwd . "errors/IError.php");
require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/Category.php");
require_once($rootCwd . "models/UnitMeasure.php");
require_once($rootCwd . "models/Supplier.php");

use Models\Category;
use Models\Item;
use Models\Supplier;
use Models\UnitMeasure;
use SemiorbitGuid\Guid;

$security = new Security();
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_WRITE);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());                        // For encryption/decryption etc..
$security->BlockNonPostRequest();                   // make sure that this script file will only execute on POST

$db     = new DbHelper($pdo);                           // Db helpwer wraps CRUD operations as functions
$fields = Item::getFields();

$itemKey = $_POST['item-key'] ?? "";

if (empty($itemKey))
    throwError();

$supplierTable = TableNames::suppliers;
$categoryTable = TableNames::categories;
$unitsTable    = TableNames::unit_measures;

// Required Data
$data = 
[
    $fields->itemName       => $_POST['item-name']      ?? "",
    $fields->itemCode       => $_POST['item-code']      ?? "", 
    $fields->criticalLevel  => $_POST['reserve-stock']  ?? "",
];

$categoryLabel  = $_POST['category-label']  ?? ""; 
$categoryKey    = $_POST['category-key']    ?? ""; 
$categoryIcon   = $_POST['icn-category']    ?? "";

$unitLabel      = $_POST['units-label']     ?? "";
$unitKey        = $_POST['units-key']       ?? "";

if (bothEmpty($categoryLabel, $categoryKey) || bothEmpty($unitLabel, $unitKey))
    throwError();

foreach (array_values($data) as $v)
{
    if (empty($v))
    {
        throwError();
        break;
    }
}

// Optional Data 
$data[$fields->remarks] = $_POST['description']     ?? "";
$supplierLabel          = $_POST['supplier-label']  ?? "";
$supplierKey            = $_POST['supplier-key']    ?? "";
$expiryDate             = $_POST['expiry-date']     ?? "";
 
if (!empty($expiryDate) && ( Dates::isPast($expiryDate) || (Dates::toString($expiryDate) == Dates::dateToday()) ))
{
    writeError("Expiration date should NOT be a past date or equal to the current date.");
    exit;
} 

$data[$fields->expiryDate] = $expiryDate; 

try
{
    // Process the category. If Key is not empty, we assume that
    // the category was selected from the picker. 
    // Otherwise, we will retrieve the category from the label and
    // register it onto the database. Finally, we will get it's id.
    if (!empty($categoryKey))
    {
        $key = $security->Decrypt($categoryKey);
        $data[$fields->category] = $key;
    }  
    else
    {
        $c = Category::getFields();

        $categoryLabel = trim($categoryLabel);
 
        $db->query("INSERT IGNORE INTO $categoryTable($c->name) VALUES('$categoryLabel')");
        $categoryId = $db->getValue($categoryTable, $c->id, [$c->name => $categoryLabel] );

        $data[$fields->category] = $categoryId;
    }

    // Process the unit measures. We will follow the same approach from categories
    if (!empty($unitKey))
    {
        $key = $security->Decrypt($unitKey);
        $data[$fields->unitMeasure] = $key;
    }
    else
    {
        $u = UnitMeasure::getFields();

        $unitLabel = trim($unitLabel);
 
        $db->query("INSERT IGNORE INTO $unitsTable($u->measurement) VALUES('$unitLabel')");
        $unitId = $db->getValue($unitsTable, $u->id, [$u->measurement => $unitLabel]);

        $data[$fields->unitMeasure] = $unitId;
    }

    // Process the suppliers. We will use the same approach above.
    // Supplier information is optional
    if (!bothEmpty($supplierLabel, $supplierKey))
    {
        if (!empty($supplierKey))
        {
            $key = $security->Decrypt($supplierKey);
            $data[$fields->supplierId] = $key;
        }
        else
        {
            $s = Supplier::getFields();

            $supplierLabel = trim($supplierLabel);
        
            $db->query("INSERT IGNORE INTO $supplierTable($s->name) VALUES('$supplierLabel')");
            $supplierId = $db->getValue($supplierTable, $s->id, [$s->name => $supplierLabel]);

            $data[$fields->supplierId] = $supplierId;
        }
    }
    
    // Check uploaded image
    $image = uploadItemImage();

    if (!empty($image))
    {
        $fileName = $image['newName'];

        // Move the image into the 'uploads' folder
        move_uploaded_file($image['tmpName'], $image['dropOff'] . $fileName);

        $data[$fields->itemImage] = $fileName;
    }

    $itemId = $security->Decrypt($itemKey);
    $db->update(TableNames::inventory, $data, [$fields->id => $itemId]);

    Response::Redirect( (ENV_SITE_ROOT . Pages::MEDICINE_INVENTORY),
        Response::Code200, 
        "An item's information was successfully updated.",
        'items-actions-success-msg'
    );
    exit;
}
catch (\Exception $ex) 
{ 
    $error = $ex->getMessage();
    
    cacheLastInputs($data);
 
    if (IString::contains($error, "for key '$fields->itemName'"))
        writeError("Item name is taken. Please try another");

    else if (IString::contains($error, "for key '$fields->itemCode'"))
        writeError("Item Code/SKU is taken. Please try another");
 
    throwError(); 
} 

// Throw an Error then stop the script
function throwError()
{
    IError::Throw(500);
    exit;
} 

function bothEmpty($data1, $data2)
{
    return (empty($data1) && empty($data2));
}


function uploadItemImage()
{
    // Check an image was uploaded 
    if ($_FILES['item-image']["error"] === 4)
        return [];

    $itemImage = $_FILES['item-image']["name"];
 
    // Validate file extension
    $allowedExtensions  = ['jpg', 'jpeg', 'png'];
    $imageExtension     = explode(".", $itemImage);
    $extension          = strtolower(end($imageExtension));

    if (!in_array($extension, $allowedExtensions))
        writeError("Image format must be in JPEG (.jpeg/.jpg) or PNG. Please try another");

    // Validate file size
    $maxSize = 2 * 1024 * 1024;

    if ($_FILES['item-image']["size"] > $maxSize)
        writeError("Image size is too large. Please choose another and make sure that the file size must not exceed 2 MB.");

    // Give the image a new unique name.
    // Get the temp file name.
    // Then return those filenames
    return [
        'tmpName' => $_FILES['item-image']['tmp_name'],
        'dropOff' => BASE_DIR . 'storage/uploads/items/',
        'newName' => Guid::NewGuid('-', false) .".". $extension
    ];
}

function writeError($msg)
{
    global $data;

    cacheLastInputs($data);

    Response::Redirect((ENV_SITE_ROOT . Pages::EDIT_ITEM), Response::Code500,
        $msg,
        'items-actions-error-msg'
    );
    exit; 
}

function cacheLastInputs($data)
{
    global $itemKey,
    $supplierKey, $supplierLabel, $unitKey, $unitLabel, 
    $categoryKey, $categoryLabel, $categoryIcon;

    $_SESSION['edit-item-last-inputs'] = $data;
    $_SESSION['edit-item-last-key'] = $itemKey;

    $_SESSION['add-item-last-special-data'] = json_encode([
        'supplierKey'   => $supplierKey, 
        'supplierLabel' => $supplierLabel,
        'unitsKey'      => $unitKey,     
        'unitsLabel'    => $unitLabel,
        'categoryKey'   => $categoryKey, 
        'categoryLabel' => $categoryLabel, 
        'categoryIcon'  => $categoryIcon
    ]);
}