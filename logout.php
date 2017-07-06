<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();


// perform a complete loggout including session variables and local cookies.
if (isset($_GET['logout'])) {

    // delete any serialized files that may exist for this patron
    unlink($_SERVER['DOCUMENT_ROOT'] . "/store/patron/checkoutData." . session_id());
    unlink($_SERVER['DOCUMENT_ROOT'] . "/store/patron/finesData." . session_id());
    unlink($_SERVER['DOCUMENT_ROOT'] . "/store/patron/historyData." . session_id());
    unlink($_SERVER['DOCUMENT_ROOT'] . "/store/patron/holdsData." . session_id());

    // clear all session variables
    $_SESSION = array();

    // set a session variable so the index page will know to display a "logged out" message.
    $_SESSION['just-logged-out'] = true;
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>
    <title><?php echo $config->getInstitutionName() ?> | Logout</title>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well">
    <div class="jumbotron">
        <h1>Logout?</h1>
        <p>You're currently logged in as <?php echo $_SESSION['patronName'] ?>.</p>
        <p>You must log out first if you want to log in as someone else.</p>
        <p><a class="btn btn-primary btn-lg" href="logout.php?logout=true" role="button"><span class="glyphicon glyphicon-exit"></span> Log Out Now</a></p>
    </div>
    
</div>

<?php include 'includes/footer.php' ?>
<?php include "includes/logoutmessage.php" ?>
</body>
</html>