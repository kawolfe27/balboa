<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-readinghistory.php");

// grab the history data for this patron
$serialData = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/historyData." . session_id());
$historyData = unserialize($serialData);
$historyData->downloadReadingList();
