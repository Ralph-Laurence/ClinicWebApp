<?php

use Models\User;

 @session_start();

require_once("rootcwd.inc.php");

global $rootCwd;

require_once($rootCwd . "includes/system.php");
require_once($rootCwd . "includes/utils.php"); 
require_once($rootCwd . "includes/urls.php");

require_once($rootCwd . "includes/Auth.php");
require_once($rootCwd . "includes/Security.php");
require_once($rootCwd . "errors/IError.php");

require_once($rootCwd . "models/User.php");

require_once($rootCwd . "MasterLayout.php");
require_once($rootCwd . "layout-header.php");

$security = new Security(); 

$db = new DbHelper($pdo);
 
function checkRecords()
{ 
	global $db;

	if (isset($_SESSION['checkup-guard-target']))
	{
		try 
		{
			$targets = $_SESSION['checkup-guard-target'];
 
			$actions = 
			[
				"Doctors" 	=> ["actionText" => "Doctors", 		"action" => Pages::REGISTER_DOCTOR],
				"Medicines" => ["actionText" => "Inventory", 	"action" => Pages::REGISTER_ITEM],
				"Patients" 	=> ["actionText" => "Patients", 	"action" => Pages::REGISTER_PATIENT],
				"Illnesses" => ["actionText" => "Illnesses", 	"action" => Pages::ILLNESS]
			];

			echo <<<DIV
				<h5 class="text-muted mb-3">Ooops!</h5>
				<p>You cannot access the checkup form without these required data.</p>
			DIV;
	
			foreach($targets as $k)			
			{
				showWarningStrip($k, $actions[$k]['action']);
			}
 
			unset($_SESSION['checkup-guard-target']);
		} 
		catch (\Throwable $th) 
		{
			IError::Throw(500);
			exit;
		}
	}
	else
	{
		$target = Pages::CHECKUP_FORM;

		echo <<<DIV
			<a href="$target" role="button" class="btn btn-base btn-secondary fw-bold">Continue</a>
		DIV;
		//Response::Redirect(Pages::HOME, Response::Code200);
	}
}

function showWarningStrip($subject, $action)
{
	$message = "No $subject were found in the system. Please click on the register button to add one.";

	echo <<<DIV
	<div class="row mb-3 overflow-hidden rounded-3 bg-white shadow-3-strong">
		<div class="col-1 d-flex align-items-center flex-row gap-2 ps-0">
			<div class="h-100 strip-cap bg-amber-300 me-2" style="width: 12px;"></div>
			<i class="fas fa-exclamation-triangle font-brown"></i>
		</div>
		<div class="col pe-3 py-3 ps-0 text-wrap">$message</div>
		<div class="col-2 text-end p-3">
			<a href="$action" role="button" class="btn btn-sm btn-secondary fw-bold">Register</a>
		</div>
	</div>
	DIV;
}