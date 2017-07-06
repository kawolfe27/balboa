<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

$alertmessage = "All fields are required.";
$alertType = "alert-info";

if (isset($_SESSION["barcode"])) {
    include "classes/sierra-patron.php";
    $thisPatron = new sierraPatron($_SESSION["barcode"],$_SESSION["pin"]);
} else {
    header("location: login.php?referrer=purchaserequest");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['comment'])) {
        if (strpos($_POST["comment"], 'damn') !== FALSE) {
            $alertmessage = "The queen will have your head for this!";
            $alertType = "alert-danger";
        }
    } else {
        sendEmail();
    }

}

function sendEmail() {

    global $alertmessage;
    global $alertType;
    $name = $_POST["name"];
    $barcode = $_POST["barcode"];
    $email = $_POST["email"];

    $thisMessage = "<h3>Here is a purchase request submitted by a Balboa University Library patron:</h3> \r\n\r\n" .
        "Name: " . $name . "\r\n" .
        "Barcode: " . $barcode . "\r\n" .
        "Email: " . $email . "\r\n" .
        "Title: " . $_POST["title"] . "\r\n" .
        "Author: " . $_POST["author"] . "\r\n" .
        "ISBN:  " . $_POST["isbn"] . "\r\n";
    include "classes/kellsmail.php";
    $purchaseRequestEmail = new kellsmail("Balboa University Library Purchase Request",$thisMessage);
    $purchaseRequestEmail->sendMail();
    if ($purchaseRequestEmail->emailSuccessful() == true) {
        $alertmessage = "Thank you, " . $name . ", for your suggestion. An email to " . $purchaseRequestEmail->getRecipient() . " has been sent successfully.";
        $alertType = "alert-success";
    } else {
        $alertmessage = "Your queen expects her subjects to be able to fill out a simple form.";
        $alertType = "alert-danger";
    }
}

?>

    <!Doctype HTML>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Balboa University Library | Purchase Request</title>
        <?php include 'includes/head-link.php' ?>
        <?php include 'includes/header.php' ?>
    </head>
    <body>
    <div class="container well">
        <form class="form-horizontal" id="email_form" action="" method="post">
            <fieldset>

                <!-- Form Name -->
                <legend>Balboa University Library Purchase Request</legend>

                <div class="alert <?php echo $alertType ?>" role="alert"><?php echo $alertmessage ?></div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="name">First and Last Name</label>
                    <div class="col-md-4">
                        <input id="name" name="name" type="text" placeholder="first and last name" class="form-control input-md" value="<?php echo $_SESSION["patronName"] ?>" autofocus required="">

                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="barcode">Library Barcode Number</label>
                    <div class="col-md-4">
                        <input id="barcode" name="barcode" pattern="[0-9]{13}[0-9x]{1}" maxlength="14" type="text" placeholder="14 digit number" class="form-control input-md" value="<?php echo $_SESSION["barcode"] ?>" required="">

                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="email">Email Address</label>
                    <div class="col-md-4">
                        <input id="email" name="email" type="text" placeholder="name@domain.com" class="form-control input-md" required="" value="<?php echo $_SESSION["patronEmail"]?>">

                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="title">Book Title</label>
                    <div class="col-md-4">
                        <input id="title" name="title" type="text" placeholder="complete title" class="form-control input-md" required="">

                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="author">Author</label>
                    <div class="col-md-4">
                        <input id="author" name="author" type="text" placeholder="first and last name of author" class="form-control input-md" required="">

                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="isbn">ISBN Number</label>
                    <div class="col-md-4">
                        <input id="isbn" name="isbn" type="text" pattern="978[0-9]{9}[0-9Xx]{1}|[0-9]{9}[0-9Xx]{1}" maxlength="13" placeholder="isbn" class="form-control input-md" required="">
                        <span class="help-block">10 or 13 digit number without the dash</span>
                    </div>
                </div>

                <!-- Button (Double) -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="request submit">Submit Request</label>
                    <div class="col-md-8">
                        <button id="request" type="submit" name="request" class="btn btn-success">Submit</button>
                        <button id="clearForm" type="button" name="cancel" class="reset btn btn-danger" onclick="clearFields();">Clear Form</button>
                    </div>
                </div>

            </fieldset>
        </form>
    </div>
    </body>
    </html>
<?php include "includes/footer.php" ?>

<script>
    $("#clearForm").click(function() {
        $(this).closest('form').find("input[type=text], textarea").val("");
    });
</script>
