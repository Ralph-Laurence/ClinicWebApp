<?php 
@session_start();

require_once("rootcwd.inc.php");
require_once($cwd . "env.php");
require_once($cwd . "includes/urls.php");
require_once($cwd . "includes/system.php");

if (!headers_sent())
{
    header("HTTP/1.1 500 Internal Server Error"); 
}

$goback = ENV_SITE_ROOT . Pages::HOME;

if (!isset($_SESSION['sec-err'])) 
{
    Response::Redirect($goback, Response::Code200);
    exit;
}
else
    unset($_SESSION['sec-err']);

// Error Code
$errCode = "SEC_ERR_UNKNOWN";

if (isset($_SESSION['sec-err-code']))
{
    $errCode = $_SESSION['sec-err-code'];
    unset($_SESSION['sec-err-code']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Error</title>
    <link rel="stylesheet" href="../assets/lib/mdb5/css/mdb.min.css">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/common.css">
    <style>
        html, body { background-color: #FCC33B; }
        .fs-5, .err-code { color: #66000F; }
        .a-btn { background-color: #0060DF !important; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row pt-5">
            <div class="col"></div>
            <div class="col-6">
                <div class="bg-white container-fluid rounded-5 shadow-2-strong p-4">
                    
                    <div class="row">
                        <div class="col-1 align-middle">
                            <img src="../assets/images/icons/security-error.png" width="36" height="36">
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center h-100 w-100 text-start">
                                <div class="fs-5 fon-fam-special">Security Error</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col">
                            <div class="my-2">
                            There was a problem with the system's security mechanism and
                            the execution was halted to prevent unauthorized access.<br><br>
                            If you continue to see this error mesage, please contact the 
                            person who manages your server.
                            </div>
                            <div class="my-3 gap-2 flex-row d-flex align-items-center">
                                <img src="../assets/images/icons/sec-err-code.png" width="24" height="24">
                                <div class="fs-6">Error Code:</div>
                                <div class="fs-6 err-code fst-italic text-uppercase"><?= $errCode ?></div>
                            </div>

                            <a class="btn btn-primary a-btn" href="<?= $goback ?>" role="button">Back To Safety</a>
                        </div>
                    </div>


                </div>
            </div>
            <div class="col"></div>
        </div>
    </div>
    <script src="../assets/lib/mdb5/js/mdb.min.js"></script>
</body>
</html>