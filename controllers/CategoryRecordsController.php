<?php

use Models\Category;
 
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
require_once($rootCwd . "models/Category.php"); 

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");
 
$security   = new Security();
$security->requirePermission(Chmod::PK_MAINTENANCE, Chmod::FLAG_READ);
$security->checkAccess(Chmod::PK_MAINTENANCE, UserAuth::getId());

$db       = new DbHelper($pdo);
$category = new Category($db);

try 
{
    $dataset = $category->showAll();
    $icons = $category->getIcons();

    $totalCategories = count($dataset);

    //dump($icons);
 
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
    $fields = Category::getFields();

    if (empty($dataset))
        return;
  
    foreach ($dataset as $row)
    {  
        $recordId = $security->Encrypt($row[$fields->id]);
        $icon     = ENV_SITE_ROOT . "assets/images/inventory/". $row[$fields->icon] . ".png";
        $iconKey  = $security->Encrypt($row[$fields->icon]);
        $dateEntry = !empty($row[$fields->dateCreated]) ? Dates::toString($row[$fields->dateCreated], "M. d, Y") : "Unknown";

        if (empty($desc))
            $desc = "No description available";
             
        echo <<<TR
            <tr>
                <!--td style="min-width: 30px; max-width: 30px; width: 30px;">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="row-check-box" />
                    </div>
                </td-->
                <td class="px-2 text-center mx-0 row-check-parent">
                    <div class="d-inline">
                        <input class="form-check-input px-0 mx-0" type="checkbox" id="row-check-box" value="" />
                    </div>
                </td>
                <td class="text-truncate th-180">
                    <div class="d-flex align-items-center">
                        <div class="ms-0">
                            <img src="$icon" class="me-3 td-category-icon" alt="icon" width="28" height="28">
                        </div>
                        <div class="font-primary-dark text-truncate td-catg-name">{$row[$fields->name]}</div> 
                    </div>
                </td>
                <td class="th-100 td-total-items">{$row['totalItems']}</td>
                <td class="th-100 text-center">
                    <div class="d-flex justify-content-center align-items-center gap-2">
                        <button type="button" class="btn py-1 px-2 btn-secondary btn-category-info">
                            <i class="fas fa-info-circle"></i>
                        </button>
                        <button type="button" class="btn py-1 px-2 btn-secondary btn-edit-category"
                            data-mdb-toggle="modal" data-mdb-target="#categoryModal">
                            <i class="fas fa-pen"></i>
                        </button>
                        <button type="button" class="btn py-1 px-2 btn-secondary btn-delete-category">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
                <td class="d-none">
                    <input type="text" class="record-key" value="$recordId"/>
                </td>
                <td class="d-none">
                    <input type="text" class="td-icon-key" value="$iconKey"/>
                    <input type="text" class="td-date-entry" value="$dateEntry"/>
                </td>
            </tr>
        TR;
    } 
}

function getSuccessMessage()
{
    $msg = "";

    if (isset($_SESSION['category-actions-success-msg']))
    {
        $msg = $_SESSION['category-actions-success-msg'];
        unset($_SESSION['category-actions-success-msg']);
    }

    return $msg;
}

function getErrorMessage()
{
    $msg = "";

    if (isset($_SESSION['category-actions-error-msg']))
    {
        $msg = $_SESSION['category-actions-error-msg'];
        unset($_SESSION['category-actions-error-msg']);
    }

    return $msg;
}

function bindIconPicker()
{
    global $icons, $security;

    if (empty($icons))
        return;

    foreach ($icons as $raw => $sanitized)
    { 
        if (empty($raw))
            continue;

        // Encrypt the icon name
        $value = $security->Encrypt($raw);

        echo <<<LI
        <li class="list-group-item icon-picker-item p-1">
            <input type="hidden" class="icon-key" value="$value" />
            <div class="d-flex align-items-center flex-column gap-2">
                <img src="assets/images/inventory/$raw.png" class="icon-image" width="48" height="48">
                <div class="w-100 text-break fsz-14 text-center text-capitalize icon-name">
                    $sanitized
                </div>
            </div>
        </li>
        LI;
    }
}