<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();


// default alert values
$alertMessage = "All fields are required.";
$alertType = "alert-info";

// if the patron has not logged in, it will redirect them to the login page.
if (!isset($_SESSION['barcode'])) {
    header("location: login.php");
    exit();
}

// we've arrived at this page because they submitted the form
if (isset($_POST['oldpin']))
{
    switch (true) {
       // if the old pin as entered on the form doesn't match the
        // actual patron's PIN they logged in with
        case ($_POST['oldpin'] != $_SESSION['pin']) : {
            $alertMessage = "Please verify your current PIN.";
            $alertType = "alert-danger";
            break;
        }
        // if the two new pins don't match
        case ($_POST['newpin'] != $_POST['verifypin']) : {
            $alertMessage = "New PINs do not match.";
            $alertType = "alert-danger";
            break;
        }
        // the form is valid so we change the pin
        default : {
            include_once ($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-patron.php");
            $result = sierraPatron::setPatronPin($_SESSION['patronId'],$_POST['newpin']);
            // if the result from the API is null - then the id change was successful.  Otherwise we get an error message in the result.
            if (is_null($result)) {
                $_SESSION['pin-just-changed'] = 'true';
                header("location: account.php");
            } else {
                $alertMessage = "Sierra error: " . $result['description'];
                $alertType = "alert-danger";
            }

        }
    }
}
?>
<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>

    <title><?php echo $config->getInstitutionName() ?> | Change PIN</title>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well">
    <form class="form-horizontal" method="post">
        <fieldset>
            <!-- Form Name -->
            <legend>Change Your Account PIN Number</legend>

            <div class="alert <?php echo $alertType ?>" role="alert"><?php echo $alertMessage ?></div>

            <!-- Old Password input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="pin">Old PIN</label>
                <div class="col-md-4">
                    <input id="oldpin" name="oldpin" type="password" placeholder="Old Library PIN Number" class="form-control input-md" required="" autofocus>
                    <span class="help-block">If you don't remember your PIN, please <a href="mailto:kelly.wolfe@iii.com?Subject=Forgotten%20Password">contact Circulation.</a></span>
                </div>
            </div>

            <!-- New Password input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="pin">New PIN</label>
                <div class="col-md-4">
                    <input id="newpin" name="newpin" type="password" placeholder="New Library PIN Number" class="form-control input-md" pattern="^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$" required="" >
                    <span class="help-block">4 or more alphanumeric characters.</a></span>
                </div>
            </div>

            <!-- Verify New Password input-->
            <div class="form-group">
                <label class="col-md-4 control-label" for="pin">Verify New PIN</label>
                <div class="col-md-4">
                    <input id="verifypin" name="verifypin" type="password" placeholder="Verify New Library PIN Number" class="form-control input-md" required="">
                    <span class="help-block">Retype your new PIN.</a></span>
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
                    >
                        Change PIN
                    </button>
                    <button id="clear" name="clear" type="button" class="btn btn-danger" onclick="window.location.href='account.php';">Cancel</button>
                </div>
            </div>

        </fieldset>
    </form>
</div>

<?php include 'includes/footer.php' ?>


</body>
</html>