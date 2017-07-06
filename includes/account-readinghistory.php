<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/sierra-readinghistory.php');
$historyData = new sierraPatronHistory($_SESSION['patronId']);

// serialize the data so we don't have to re-read all the API data when we re-sort the contents
$serialData = serialize($historyData);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/historyData." . session_id(), $serialData);

if ($historyData->getTotalHistory() == 0) {
    echo '<br>';
    echo 'You currently have no reading history at the ' . $config->getInstitutionName() . '.';
} else {
    echo '<div id="history-data">';
        include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/account-readinghistory-rows.php");
    echo '</div>';
}