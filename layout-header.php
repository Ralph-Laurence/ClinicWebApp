<?php 
require_once("env.php");
 
// Include MasterLayout.php before this 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clinic Information System</title>
 
    <link rel="stylesheet" href="<?= ENV_SITE_ROOT . "assets/lib/mdb5/css/mdb.min.css" ?>">
    <link rel="stylesheet" href="<?= ENV_SITE_ROOT . "assets/lib/fontawesome/css/all.css" ?>">
    <link rel="stylesheet" href="<?= ENV_SITE_ROOT . "assets/lib/jquery-ui-1.13.2.custom/jquery-ui.min.css" ?>"> 
    <link rel="stylesheet" href="<?= ENV_SITE_ROOT . "assets/lib/simplebar/simplebar.min.css" ?>">
    <link rel="stylesheet" href="<?= ENV_SITE_ROOT . "assets/css/root.css" ?>">
    <link rel="stylesheet" href="<?= ENV_SITE_ROOT . "assets/css/common.css" ?>">
    <link rel="stylesheet" href="<?= ENV_SITE_ROOT . "assets/css/components.css" ?>">
    <link rel="stylesheet" href="<?= ENV_SITE_ROOT . "assets/css/overrides.css" ?>">

    <?php 
    if (isset($masterPage))
        $masterPage->loadStyles(); 
    ?>
</head>