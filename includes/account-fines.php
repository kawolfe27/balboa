<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

include_once($_SERVER['DOCUMENT_ROOT'] .  '/classes/sierra-finesandfees.php');
$finesData = new sierraPatronFines($_SESSION['patronId']);

// serialize the data so we don't have to re-read all the API data when we re-sort the contents
$serialData = serialize($finesData);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/finesData." . session_id(), $serialData);

if ($finesData->getTotalFines() == 0) {
    echo '<br>';
    echo 'You currently have no fines at the ' . $config->getInstitutionName() . '.';
} else {
    echo '<div id="fines-data">';
        include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/account-fines-rows.php");
    echo '<div>';
}
