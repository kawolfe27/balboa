<?php

session_start();

/* retrieve the bib data for the faculty publications carousel/list  */
include_once('root-config.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/carousel-bibs.php');
$carouselBibQuery = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/json/' . $config->getCarouselQueryFilename());
$carouselBibs = new carouselBibs($carouselBibQuery);
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>
    <title><?php echo $config->getInstitutionName() ?> | Faculty Publications List</title>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container-fluid">
    <?php include "includes/content-faculty-pubs-list.php"; ?>
</div>
<?php include 'includes/footer.php' ?>
</body>
</html>