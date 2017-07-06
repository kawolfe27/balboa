<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
$config = new config();

// default alert values
$alertMessage = "All fields are required.";
$alertType = "alert-info";

// if the patron is already logged in, then take them to a logout page.
if (isset($_SESSION['barcode'])) {
    header("location: logout.php");
}

// message to anyone who needs to login first
if (isset($_GET['referrer'])) {
    switch ($_GET['referrer']) {
        case "bibdetail" :
            $alertMessage = "You must be logged in as a " . $config->getInstitutionName() . " patron to place a hold.";
            $alertType = "alert-danger";
            break;
    }
}

// validate patron credentials if we've been passed login data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include "classes/sierra-patron.php";
    $loggedPatron = new sierraPatron($_POST["barcode"],$_POST["pin"]);
    if ($loggedPatron->isValidPatron() == true) {
        // credentials are valid.  load basic data into session variables.
        $_SESSION['barcode'] = $_POST['barcode'];
        $_SESSION['pin'] = $_POST['pin'];
        $_SESSION['patronName'] = $loggedPatron->getFirstNameLastName();
        $_SESSION['patronId'] = $loggedPatron->getPatronID();
        $_SESSION['patronEmail'] = $loggedPatron->getPatronEmail(0); // use the first email in the record.
        // now exit login page and go home.

        switch ($_GET['referrer']) {
            case "purchaserequest" :
                header("Location: purchaserequest.php");
                break;

            case "bibdetail" :
                header("Location: bibdetail.php?bibid=" . $_GET['bibid']);
                break;
            default :
                header("Location: account.php");

        }

        exit;
    } else {
        // patron login was INvalid
        $alertMessage = "Patron login is invalid.  Please try again.";
        $alertType = "alert-danger";
    }
}
?>
<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>
    <title><?php echo $config->getInstitutionName() ?> | Login</title>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well">
    <form class="form-horizontal" method="post">
        <fieldset>
            <!-- Form Name -->
            <legend>Login to <?php echo $config->getInstitutionName() ?></legend>

            <div class="alert <?php echo $alertType ?>" role="alert"><?php echo $alertMessage ?></div>
            <!-- Text input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="barcode">Barcode</label>
                <div class="col-md-4">
                    <input id="barcode" name="barcode" type="text" autofocus placeholder="14 digit library barcode number" class="form-control input-md" maxlength="14" required="">
                </div>
            </div>

            <!-- Password input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="pin">PIN</label>
                <div class="col-md-4">
                    <input id="pin" name="pin" type="password" placeholder="Library PIN number" class="form-control input-md" required="">
                    <span class="help-block">If you don't remember your PIN, please <a href="mailto:kelly.wolfe@iii.com?Subject=Forgotten%20Password">contact Circulation.</a></span>
                </div>
            </div>

            <!-- Button (Double) -->
            <div class="form-group">
                <label class="col-md-4 control-label" for="submit"></label>
                <div class="col-md-8">
                    <button
                        id="submit"
                        name="submit"
                        type="submit"
                        class="btn btn-success"
                        data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i>"
                    >
                        Login
                    </button>
                    <button id="clear" name="clear" type="button" class="btn btn-danger" onclick="window.history.back();">Cancel</button>
                </div>
            </div>

        </fieldset>
    </form>
</div>

<?php include 'includes/footer.php' ?>
</body>
</html>