<?php
session_start();

include_once('root-config.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/locale-strings.php');
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>
    <title><?php echo $config->getInstitutionName() ?> | Home</title>
</head>
<body style="padding: 0">
<?php include "includes/header.php" ?>

<?php
/* retrieve the bib data for the faculty publications carousel/list  */
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/carousel-bibs.php');
$carouselBibs = new carouselBibs($config);
?>

<div class="container-fluid main-container">

    <!-- the top section of the page -->

    <!-- the search bento box -->
    <div class="bento-container visible-lg-* visible-md-* hidden-sm hidden-xs">
        <div class="well bento-block transparent">
            <?php include "includes/content-bento-searchbox.php"; ?>
        </div>
    </div>

    <!-- the searches in accordions (for small footprint devices) -->
    <div class="visible-xs-* visible-sm-* hidden-md hidden-lg">
        <?php include "includes/content-bento-searchbox-accordion.php"; ?>
    </div>


    <!-- the featured titles LIST view (for small footprint devices) -->
    <div class="visible-xs-* visible-sm-* hidden-md hidden-lg">
        <?php include "includes/content-featured-titles-list.php"; ?>
    </div>

    <!-- here starts a two column section -->

    <!-- the LEFT column -->
    <div class="container">

        <div class="col-xs-12 col-md-6">
            <div class="main-page-content-block visible-lg-* visible-md-* hidden-sm hidden-xs">
                <?php include "includes/content-featured-titles-carousel.php"; ?>
            </div>
            <?php include "includes/content-hours.php"; ?>
        </div>

        <!-- this is the RIGHT column -->

        <div class="col-xs-12 col-md-6">
            <?php include "includes/content-popular.php"; ?>
            <?php include "includes/content-news.php"; ?>
        </div>
    </div>
</div>
<?php include 'includes/footer.php' ?>

<!-- to provide support for the waterwheel carousel -->
<script src="js/jquery.waterwheelCarousel.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#carousel").waterwheelCarousel({
            autoPlay: 10000,
            flankingItems: 3,
            separation: 100
        });
    });</script>

<?php
// if we got here via the registration process, display a message to the user.
if (isset($_SESSION['registration-succeeded'])) {
    if ($_SESSION['registration-succeeded'] == false) {
        include "includes/modal-registration-failed.php";
        echo "<script> $('#failedRegistrationModal').modal('show');</script>";
        unset($_SESSION['registration-succeeded']);
    }
}


// if we got here via the logout process, display a message to the user.
if (isset($_SESSION['just-logged-out'])) {
    if ($_SESSION['just-logged-out'] == true) {
        include "includes/logged-out.php";
        echo "<script> $('#loggedOutModal').modal('show');</script>";
        unset($_SESSION['just-logged-out']);
    }
}

// if we got here via the contact/comment page, display a message to the user.
if (isset($_SESSION['just-left-a-comment'])) {
    if ($_SESSION['just-left-a-comment'] == true) {
        include "includes/left-a-comment.php";
        echo "<script> $('#leftACommentModal').modal('show');</script>";
        unset($_SESSION['just-left-a-comment']);
    }
}

?>

<script>
    // delay load the carousel images
    function init() {
        var imgDefer = document.getElementsByTagName('img');
        for (var i=0; i<imgDefer.length; i++) {
            if(imgDefer[i].getAttribute('data-src')) {
                imgDefer[i].setAttribute('src',imgDefer[i].getAttribute('data-src'));
            } } }
    window.onload = init;
</script>
</body>
</html>
