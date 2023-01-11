<?php 
@session_start();

$welcomeName = "";

if (isset($_SESSION['firstname']))
    $welcomeName = $_SESSION['firstname'];
?>
<div class="bg-secondary welcome-banner bg-control d-flex align-items-center px-4 fw-bold fs-6">
    <button type="button" class="btn border-0 fs-6 px-2 py-1 shadow-0 me-2 display-none btn-show-sidenav"
    data-mdb-toggle="tooltip" title="Click to show Navigation">
        <i class="fas fa-bars"></i>
    </button>
    <span class="welcome-text me-auto">Welcome <?= $welcomeName ?>, have a nice day!</span>
    <span class="banner-date-text"><?php echo Dates::dateToday("l, F d, Y, h:i A"); ?></span>
</div>