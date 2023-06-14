<?php

use Models\Supplier;

@session_start();

require_once("rootcwd.php");
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "database/configs.php");
require_once($rootCwd . "database/dbhelper.php");
require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php");  

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");
require_once($rootCwd . "models/Supplier.php"); 

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");
 
$security   = new Security();
$security->requirePermission(Chmod::PK_SUPPLIERS, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_SUPPLIERS, UserAuth::getId());

$db       = new DbHelper($pdo);
$supplier = new Supplier($db);

try 
{
    $dataset = $supplier->getAll();
    $totalSuppliers = count($dataset);
} 
catch (\Exception $ex) { onError(); }
catch (\Throwable $ex) { onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}

function bindDataset()
{
    global $dataset, $security;
    $fields = Supplier::getFields();

    if (empty($dataset))
        return;
  
    foreach ($dataset as $row)
    {  
        $supplierId = "SUP-" . str_pad($row[$fields->id], 4, "0", STR_PAD_LEFT);
        $recordId = $security->Encrypt($row[$fields->id]);

        echo <<<TR
            <tr>
                <td class="px-2 text-center mx-0 row-check-parent">
                    <div class="d-inline">
                        <input class="form-check-input px-0 mx-0" type="checkbox" id="row-check-box" value="" />
                    </div>
                </td>
                <td class="text-truncate th-100 font-green fw-bold td-supp-id">$supplierId</td>
                <td class="text-truncate th-230 td-supp-name">{$row[$fields->name]}</td>
                <td class="text-truncate th-150">{$row[$fields->contact]}</td>
                <td class="text-truncate th-150">{$row[$fields->email]}</td>
                <td class="th-150 text-center">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary btn-details px-2 py-1 text-center">Details</button>
                        <button type="button" class="btn btn-primary btn-split-arrow px-0 py-1 text-center dropdown-toggle dropdown-toggle-split" data-mdb-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu shadow-3-strong dropdown-menu-custom-light-small">
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
                    <input type="text" class="record-key" value="$recordId"/>
                </td>
            </tr>
        TR;
    } 
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['supplier-actions-success-msg']))
    {
        $msg = $_SESSION['supplier-actions-success-msg'];
        unset($_SESSION['supplier-actions-success-msg']);
    }

    return $msg;
}

