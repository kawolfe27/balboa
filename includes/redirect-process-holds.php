<?php

session_start();

// the post variable should be an array of hold id's and the value of the button that was selected
if (isset($_POST)) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/sierra-holds.php');

    switch(true) {
        case isset($_POST['needsLogin']): {
            header("location: /login.php?referrer=bibdetail&bibid=" . $_POST['bibid']);
            break;
        }
        case isset($_POST['changePickup']): {
            changePickupLocations();
            header("location: /account.php#holds");
            break;
        }
        case isset($_POST['cancelHold']): {
            cancelSelectedHolds();
            header("location: /account.php#holds");
            break;
        }
        case isset($_POST['freezeHold']): {
            freezeSelectedHolds();
            header("location: /account.php#holds");
            break;
        }
        case isset($_POST['unFreezeHold']): {
            unFreezeSelectedHolds();
            header("location: /account.php#holds");
            break;
        }
        case isset($_POST['placeHold']): {
            placeHold();
            header("location: /bibdetail.php?bibid=" . $_POST['bibid']);
            break;
        }
    }
}

/* FUNCTION DEFINITIONS (must exist OUTSIDE conditional statements or Php doesn't see them) */

function changePickupLocations() {
    $successCounter = 0;
    foreach ($_POST as $holdId => $checkedStatus) {
        if ($checkedStatus == "on") {
            $result = sierraPatronHolds::changePickupLocation($holdId, $_POST['newLocation']);
            if ($result == "") {
                $successCounter++;
            }
        }
    }
    $_SESSION["locationsChanged"] = $successCounter;
}

function cancelSelectedHolds()
{
    $successCounter = 0;
    foreach ($_POST as $holdId => $checkedStatus) {
        if ($checkedStatus == "on") {
            $result = sierraPatronHolds::cancelAHold($holdId);
            if ($result == "") {
                $successCounter++;
            }
        }
    }
    $_SESSION["holdsCanceled"] = $successCounter;
}

function freezeSelectedHolds()
{
    $successCounter = 0;
    foreach ($_POST as $holdId => $checkedStatus) {
        if ($checkedStatus == "on") {
            $result = sierraPatronHolds::freezeAHold($holdId);
            if ($result == "") {
                $successCounter++;
            }
        }
    }
    $_SESSION["holdsFrozen"] = $successCounter;
}

function unFreezeSelectedHolds()
{
    $successCounter = 0;
    foreach ($_POST as $holdId => $checkedStatus) {
        if ($checkedStatus == "on") {
            $result = sierraPatronHolds::unFreezeAHold($holdId);
            if ($result == "") {
                $successCounter++;
            }
        }
    }
    $_SESSION["holdsUnfrozen"] = $successCounter;
}

function placeHold()
{
    $holdResponse = sierraPatronHolds::placeAHold($_SESSION['patronId'], $_POST['bibid'], $_POST['pickupLocation'], $_POST['notWantedAfter']);
    if ($holdResponse === NULL) {
        $_SESSION["holdAttemptResponse"] = 'successful';
    } else {
        $_SESSION["holdAttemptResponse"] = $holdResponse;
    }
}
