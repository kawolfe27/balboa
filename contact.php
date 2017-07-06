<?php
session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
$config = new config();

// default alert message at the top of the comment form
// we change it farther down if the context requires a different message
$alertMessage = "All fields are required.";
$alertType = "alert-info";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    logTheComment();
    if ($config->getCommentSendEmail() == '1') {
        sendEmail();
    }
    // set a session variable so the index page will know to display a "thanks for the feedback" message.
    $_SESSION['just-left-a-comment'] = true;
    header("location: index.php");
    exit;
}

function logTheComment() {
    include "classes/comment-log.php";
    $logEntry = new commentLog();
    $logEntry->writeLogEntry($_POST["name"],$_POST["email"],$_POST["reply"],$_POST["comment"]);
    return;
}

function sendEmail() {
    global $config;
    global $alertMessage;
    global $alertType;
    $name = $_POST["name"];
    $userEmail = $_POST["email"];
    $reply = ($_POST["reply"] == 1 ? 'Yes' : 'No');

    $thisMessage = "Below is a comment from a " . $config->getInstitutionName()  . " user:" . "\r\n\r\n" .
        "Username: " . $name . "\r\n" .
        "Email: " . $userEmail . "\r\n" .
        "Requests a response:  " . $reply . "\r\n" .
        "Comment: " . "\r\n\r\n" .
        $_POST["comment"];

    include "classes/kellsmail.php";
    $contactEmail = new kellsmail($userEmail,$config->getInstitutionName() . " Contact Form",$thisMessage);
    $contactEmail->sendMail();
    if ($contactEmail->emailSuccessful() == true) {
        $alertMessage = "Thank you, " .
            htmlspecialchars($name) . ", for your comment. An email to " . htmlspecialchars($contactEmail->getRecipient()) . " has been sent successfully.";
        $alertType = "alert-success";
    } else {
        $alertMessage = "Please fill out the form correctly.";
        $alertType = "alert-danger";
    }
    return;
}


?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>
    <title><?php echo $config->getInstitutionName() ?> | Contact</title>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well">

    <div class="page-header">
        <h1>Contact Us</h1>
    </div>
    <div class="well">
        <form class="form-horizontal" action="#" method="post" onsubmit="return allFieldsAreCompleted();">
            <fieldset>

                <div class="alert <?php echo $alertType ?>" role="alert"><?php echo $alertMessage ?></div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="name">Your Name</label>
                    <div class="col-md-4">
                        <input id="name" name="name" type="text" placeholder="First and last name" class="form-control input-md" required="">
                        <span class="help-block">Who are you?</span>
                    </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="email">Email Address</label>
                    <div class="col-md-4">
                        <input id="email" name="email" type="email" placeholder="email@domain.com" class="form-control input-md" required="">
                        <span class="help-block">How do we contact you?</span>
                    </div>
                </div>

                <!-- Multiple Radios -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="reply">Reply</label>
                    <div class="col-md-4">
                        <div class="radio">
                            <label for="answer-0">
                                <input type="radio" name="reply" id="answer-0" value="1" checked="checked">
                                Yes
                            </label>
                        </div>
                        <div class="radio">
                            <label for="answer-1">
                                <input type="radio" name="reply" id="answer-1" value="0">
                                No
                            </label>
                        </div>
                        <span class="help-block">Do you want a reply back from library staff?</span>
                    </div>
                </div>

                <!-- Textarea -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="comment">Comment</label>
                    <div class="col-md-4">
                        <textarea class="form-control" id="comment" name="comment" placeholder="Your question or comment for us"></textarea>
                    </div>
                </div>

                <!-- Button (Double) -->
                <div class="form-group">
                    <label class="col-md-4 control-label" for="submit"></label>
                    <div class="col-md-8">
                        <button id="submit" name="submit" class="btn btn-success" type="submit">Submit</button>
                        <button id="cancel" name="cancel" class="btn btn-danger" type="reset" onclick="window.location.href='index.php'">Cancel</button>
                    </div>
                </div>

            </fieldset>
        </form>
    </div>


</div>

<?php include 'includes/footer.php' ?>

<!--
even though HTML5 lets us say input fields are "required," Safari (and others?) doesn't support it, so we
still need to use a validation script to make sure all the fields got filled out.
-->
<script>
    function allFieldsAreCompleted() {
        if ((document.getElementById("name").value == "") ||
            (document.getElementById("email").value == "") ||
            (document.getElementById("comment").value == "")) {
            window.alert('Please complete ALL fields!');
            return false;
        } else {
            return true;
        }
    }
</script>
</body>
</html>