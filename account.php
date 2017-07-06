<?php

session_start();

include_once('root-config.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

if (isset($_SESSION["barcode"])) {
    include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-patron.php");
    $thisPatron = new sierraPatron($_SESSION["barcode"],$_SESSION["pin"]);
} else {
    header("location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>
    <title><?php echo $config->getInstitutionName() ?> | My Account</title>
    <link rel="stylesheet" href="css/print.css" type="text/css" media="print">
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well tab">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#myaccount" data-toggle="tab" class="tabText"><span class="glyphicon glyphicon-cog"></span> My Account</a></li>
        <li><a href="#checkouts" data-toggle="tab" class="tabText"><span class="glyphicon glyphicon-ok-circle"></span> Checkouts</a> </li>
        <li><a href="#holds" data-toggle="tab" class="tabText"><span class="glyphicon glyphicon-bell"></span> Holds</a></li>
        <li><a href="#finesandfees" data-toggle="tab" class="tabText"><span class="glyphicon glyphicon-piggy-bank"></span> Fines and Fees</a></li>
        <li><a href="#readinghistory" data-toggle="tab" class="tabText"><span class="glyphicon glyphicon-book"></span> Reading History</a></li>
    </ul>
    <div class="tab-content">
        <div id="myaccount" class="tab-pane fade in active">
            <?php
            include "includes/account-info.php"
            ?>
        </div>
        <div id="checkouts" class="tab-pane fade">
            <div class="image-container">
                <img class="loading-image img-responsive" src="/images/loading-traditional.gif">
            </div>
        </div>
        <div id="holds" class="tab-pane fade">
            <div class="image-container">
                <img class="loading-image img-responsive" src="/images/loading-traditional.gif">
            </div>
        </div>
        <div id="finesandfees" class="tab-pane fade">
            <div class="image-container">
                <img class="loading-image img-responsive" src="/images/loading-traditional.gif">
            </div>
        </div>
        <div id="readinghistory" class="tab-pane fade">
            <div class="image-container">
                <img class="loading-image img-responsive" src="/images/loading-traditional.gif">
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php' ?>

<script>
    $checkoutsLoaded = false;
    $holdsLoaded = false;
    $finesLoaded = false;
    $historyLoaded = false;
    /* load tab content dynamically when the tab is revealed  */
    $('a[href="#checkouts"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href"); // activated tab
        if (!$checkoutsLoaded) {
            $checkoutsLoaded = true;
            $('#checkouts').load('/includes/account-checkouts.php');
        }
    });

    $('a[href="#holds"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href"); // activated tab
        if (!$holdsLoaded) {
            $holdsLoaded = true;
            $('#holds').load('/includes/account-holds.php');
        }
    });

    $('a[href="#finesandfees"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href"); // activated tab
        if (!$finesLoaded) {
            $('#finesandfees').load('/includes/account-fines.php');
            $finesLoaded = true;
        }
    });

    $('a[href="#readinghistory"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href"); // activated tab
        if (!$historyLoaded) {
            $('#readinghistory').load('/includes/account-readinghistory.php');
            $historyLoaded = true;
        }
    });

    // support specifying an id in the referring URL and automatically opening that tab when the page is loaded.
    $(function () {
        if (location.hash != '') {
            var activeTab = $('[href=' + location.hash + ']');
            activeTab && activeTab.tab('show');
        }
    });

</script>

<?php
if (isset($_SESSION['pin-just-changed'])) {
    if ($_SESSION['pin-just-changed'] == true) {
        include "includes/pinchange.php";
        echo "<script> $('#pinChangeModal').modal('show');</script>";
        unset($_SESSION['pin-just-changed']);
    }
}
?>
</body>
</html>