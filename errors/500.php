<?php
require_once("rootcwd.inc.php");

require_once($cwd . "includes/urls.php");
require_once($cwd . "env.php");

http_response_code(500);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Information System</title>

    <!-- STYLES -->
    <link rel="stylesheet" href="../assets/lib/mdb5/css/mdb.min.css">
    <link rel="stylesheet" href="../assets/lib/fontawesome/css/all.css">
    <link rel="stylesheet" href="../assets/css/root.css">
    <link rel="stylesheet" href="../assets/css/common.css"> 

</head>
<body>

    <div class="container w-100 h-100 d-flex flex-column align-items-center justify-content-center">
        <img src="../assets/images/errors/500.png" alt="icon">
        <h5 class="mt-4"> 
            <span class="fw-bold font-base">500</span>
            <span class="mx-3 border border-end border-secondary"></span>
            <span class="text-uppercase text-muted">SERVER ERROR</span>
        </h5>
        <span class="text-muted text-center my-3">
            Something went wrong on our side.<br>Don't worry, it's not your fault.
        </span> 
        <a role="button" href="<?= ENV_SITE_ROOT . Pages::HOME ?>">
            <i class="fas fa-chevron-circle-left"></i>
            <span>Back To Safety</span>
        </a>
    </div>

    <!--SCRIPTS-->
    <script src="../assets/lib/jquery/jquery-3.6.1.min.js"></script>
    <script src="../assets/lib/mdb5/js/mdb.min.js"></script>

</body>

</html>