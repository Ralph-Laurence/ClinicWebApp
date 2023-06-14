<?php

use Models\Illness;
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
require_once($rootCwd . "models/Illness.php"); 

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");
 
$security   = new Security();
$security->requirePermission(Chmod::PK_MAINTENANCE, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_MAINTENANCE, UserAuth::getId());

$db       = new DbHelper($pdo);
$illness = new Illness($db);

try 
{
    $dataset = $illness->showAll();

    $totalIllness = count($dataset);
} 
catch (\Exception $ex) { echo $ex->getMessage(); exit; onError(); }
catch (\Throwable $ex) { echo $ex->getMessage(); exit; onError(); }

function onError()
{
    IError::Throw(500);
    exit;
}

function bindDataset()
{
    global $dataset, $security;
    $fields = Illness::getFields();

    if (empty($dataset))
        return;
  
    foreach ($dataset as $row)
    {  
        $recordId    = $security->Encrypt($row[$fields->id]);
        $desc        = $row[$fields->description];
        $illnessDate = "Created on: " . Dates::toString($row[$fields->dateCreated], "M. d, Y");

        if (empty($desc))
            $desc = "No description available";

        echo <<<TR
            <tr>
                <td class="px-2 text-center mx-0 row-check-parent">
                    <div class="d-inline">
                        <input class="form-check-input px-0 mx-0" type="checkbox" id="row-check-box" value="" />
                    </div>
                </td>
                <td class="text-truncate th-180 td-ill-name">{$row[$fields->name]}</td>
                <td class="th-100 td-total-records">{$row['totalCheckupRecords']}</td>
                <td class="th-100 text-center">
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <button type="button" class="btn py-1 px-2 btn-secondary btn-illness-info">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button type="button" class="btn py-1 px-2 btn-secondary btn-edit-illness">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button type="button" class="btn py-1 px-2 btn-secondary btn-delete-illness">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
                <td class="d-none">
                    <input type="text" class="record-key" value="$recordId"/>
                </td>
                <td class="d-none">
                    <textarea class="td-ill-desc">$desc</textarea>
                    <input type="text" class="td-ill-date" value="$illnessDate" />
                </td>
            </tr>
        TR;
    } 
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['illness-actions-success-msg']))
    {
        $msg = $_SESSION['illness-actions-success-msg'];
        unset($_SESSION['illness-actions-success-msg']);
    }

    return $msg;
}

function getErrorMessage()
{
    $msg = "";

    if (isset($_SESSION['illness-actions-error-msg']))
    {
        $msg = $_SESSION['illness-actions-error-msg'];
        unset($_SESSION['illness-actions-error-msg']);
    }

    return $msg;
}

