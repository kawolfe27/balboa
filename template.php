<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

?>s
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>
    <title><?php echo $config->getInstitutionName() ?> | {YOUR PAGE NAME HERE}</title>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well">

    <div class="col-xs-6 col-md-9">


    </div>

    <div class="col-xs-6 col-md-3">

    </div>
</div>

<?php include 'includes/footer.php' ?>
</body>
</html>