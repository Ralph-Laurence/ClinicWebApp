<?php 
@session_start();

$bannerGreetings = [
    "have a great",
    "have a productive",
    "have a nice",
    "have an awesome",
    "have a wonderful"
];

$bannerGreeting = $bannerGreetings[array_rand($bannerGreetings)];
$bannerDay = Dates::dateToday("l");
$welcomeName = "";

if (isset($_SESSION['firstname']) && strtolower($_SESSION['firstname']) != "system")
    $welcomeName = $_SESSION['firstname'];
?>
<div class="bg-secondary welcome-banner bg-control d-flex align-items-center px-3 fw-bold fs-6">
    <button type="button" class="btn border-0 fs-6 px-2 py-1 shadow-0 me-2 display-none btn-show-sidenav"
    data-mdb-toggle="tooltip" title="Click to show Navigation">
        <i class="fas fa-bars"></i>
    </button>
    <span class="welcome-text me-auto">Welcome <?= "$welcomeName, $bannerGreeting $bannerDay!" ?></span>
    <span class="banner-date-text"><?php echo Dates::dateToday("D, F d, Y, g:i A"); ?></span>
</div>