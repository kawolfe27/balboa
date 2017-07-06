<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

// display a result alert when we've been directed back to this page after a button was pushed
// and whatever appropriate processing happened.
displayAppropriateResultMessage();

// instantiate an object of holds data.
// this object persists even if we re-sort the rows
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/sierra-holds.php');
$holdsData = new sierraPatronHolds($_SESSION['patronId']);

// serialize the data so we don't have to re-read all the API data when we re-sort the contents
$serialData = serialize($holdsData);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/holdsData." . session_id(), $serialData);

if ($holdsData->getTotalHolds() == 0) {
    echo '<br>';
    echo 'You have nothing currently on hold from the ' . $config->getInstitutionName() . '.';
} else {
    echo '<div id="hold-data">';
    include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/account-holds-rows.php");
    echo '</div>';
}

function displayAppropriateResultMessage() {
    switch (true) {
        case (isset($_SESSION["holdsCanceled"])) : {
            $alertMessage = $_SESSION["holdsCanceled"] . " hold(s) canceled.";
            echo '<div class="alert alert-success" role="alert">' .
                '<a href="#" class="close" data-dismiss="alert">&times;</a>' .
                $alertMessage .
                '</div>';
            unset($_SESSION["holdsCanceled"]);
            break;
        }
        case (isset($_SESSION["holdsFrozen"])) : {
            $alertMessage = $_SESSION["holdsFrozen"] . " hold(s) frozen. <a href='#' class='more-prompt' data-toggle='tooltip' data-placement='right' title='There are a number of conditions that would prevent a hold from being frozen. Essentially, if an item is already on its way to you or if you are aligned to get it very soon, the system will block your attempt to freeze the request.'>more...</a>";
            echo '<div class="alert alert-success" role="alert">' .
                '<a href="#" class="close" data-dismiss="alert">&times;</a>' .
                $alertMessage .
                '</div>';
            unset($_SESSION["holdsFrozen"]);
            break;
        }
        case (isset($_SESSION["holdsUnfrozen"])) : {
            $alertMessage = $_SESSION["holdsUnfrozen"] . " hold(s) unfrozen.";
            echo '<div class="alert alert-success" role="alert">' .
                '<a href="#" class="close" data-dismiss="alert">&times;</a>' .
                $alertMessage .
                '</div>';
            unset($_SESSION["holdsUnfrozen"]);
            break;
        }
        case (isset($_SESSION["locationsChanged"])) : {
            $alertMessage = $_SESSION["locationsChanged"] . " pickup location(s) changed.";
            echo '<div class="alert alert-success" role="alert">' .
                '<a href="#" class="close" data-dismiss="alert">&times;</a>' .
                $alertMessage .
                '</div>';
            unset($_SESSION["locationsChanged"]);
            break;
        }
    }
}
?>
<script>
    // support tooltips
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
