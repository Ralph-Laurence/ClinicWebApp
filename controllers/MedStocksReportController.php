<?php

use Models\Checkup;
use Models\Degrees;
use Models\Doctor;
use Models\DoctorSpecialty;
use Models\Item;
use Models\Patient;
use Models\Prescription;
use Models\Stock;
use Models\UnitMeasure;

@session_start();

require_once("rootcwd.inc.php");

global $rootCwd;

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");
 
require_once($rootCwd . "models/Item.php");
require_once($rootCwd . "models/UnitMeasure.php");
require_once($rootCwd . "models/Stock.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security(); 
 
$security->requirePermission(Chmod::PK_INVENTORY, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_INVENTORY, UserAuth::getId());

$db     = new DbHelper($pdo);
$items  = TableNames::inventory;
$units  = TableNames::unit_measures;
$stocks = TableNames::stock;

$i      = Item::getFields(); 
$u      = UnitMeasure::getFields();
$s      = Stock::getFields();

try 
{ 
    $stmt_get_medicines = $db->getInstance()->prepare
    (
        "SELECT 
        i.$i->itemName     AS 'medicine',
        i.$i->remaining    AS 'qty',
        u.$u->measurement  AS 'units',
        (
            SELECT GROUP_CONCAT( DISTINCT(DATE_FORMAT(s.$s->expiry_date, '%m/%Y')) ) 
            FROM $stocks AS s WHERE s.$s->item_id = i.$i->id
        ) AS 'expiryDates'
        FROM $items AS i
        LEFT JOIN $units AS u ON u.$u->id = i.$i->unitMeasure
        ORDER BY i.$i->itemName ASC"
    );
    $stmt_get_medicines->execute();
    $medicines = $stmt_get_medicines->fetchAll(PDO::FETCH_ASSOC);
 
} 
catch (\Exception $ex) 
{
    echo $ex->getMessage(); exit;
}
 
function getSuccessMessage()
{
    $message = "";

    if (isset($_SESSION['settings-action-success']))
    {
        $message = $_SESSION['settings-action-success'];
        unset($_SESSION['settings-action-success']);
    }

    return $message;
}

function bindDataset()
{
    global $medicines;

    foreach ($medicines as $obj)
    {
        $name   = $obj['medicine'];
        $qty    = $obj['qty'];
        $units  = $obj['units'];
        $expiry = $obj['expiryDates'];
        
        $expiryDates = "";
        $remarks = "";

        if (!empty($expiry))
        {
            $exp = explode(",", $expiry);
            $spans = [];

            foreach ($exp as $e)
            { 
                if (empty($e))
                    continue;

                $date = '01/' . $e;
                $dateObject = DateTime::createFromFormat('d/m/Y', $date);
                $expiration = $dateObject->format('Y-m-d');
 
                $temp = <<<SPAN
                <span class="rounded-5 expiry-badge-sm">$e</span>
                SPAN; 

                if (Dates::isPast($expiration) || Dates::toString($expiration) == Dates::dateToday())
                {
                    $temp = <<<SPAN
                    <span class="rounded-5 expiry-badge-sm-red">$e</span>
                    SPAN; 

                    $remarks = "has expired stock(s)";
                }

                $spans[] = $temp; 
            }

            $expiryDates = implode(' ', $spans);
        }

        echo <<<TR
        <tr>
            <td>$name</td>
            <td class="border-start border-end font-primary-dark">$qty</td>
            <td class="text-muted">$units</td>
            <td class="border-start border-end px-1 text-center text-break">$expiryDates</td>
            <td class="px-1">$remarks</td>
        </tr>
        TR;
    }
}

function getLogo()
{
    return ENV_SITE_ROOT . "assets/images/logo-s.png";
}
 

function getPreparedBy($what = 'role')
{
    if ($what == 'name')
        return implode(' ', [UserAuth::getFirstname(), UserAuth::getMiddlename(), UserAuth::getLastname()]);

    $roles = 
    [
        UserRoles::SUPER_ADMIN  => "System Admin",
        UserRoles::ADMIN        => "Administrator",
        UserRoles::STAFF        => "Medical Staff"
    ];
    return $roles[UserAuth::getRole()];
}
