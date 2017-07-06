<?php
/**
 * Removes stale cached files in the current directory (files last modified over 48 hours ago).  when users logout the software clears these files as part of the logout process.  But not everyone logs out.  :)
 *
 * These cached files are serialized data from the "My Account" page/tabs.
 *
 * This script should be run as a daily cron job
 */

define("APP_ROOT","/home2/polarisd/public_html/wolfe/balboa/");

$dir = new DirectoryIterator(APP_ROOT . 'store/patron');

$deleteCounter = 0;
$cacheFilesCounter = 0;
foreach ($dir as $fileinfo) {

    // IF...
    // the current file is not . or ..
    // the current file is not the cleanup.php or cleanup.log file
    // the file was last created/updated over 24 hours ago
    // THEN...
    //
    // delete the file

    $fileHoursOld = (time() - $fileinfo->getMTime()) / 3600;

    if ($fileinfo->isDot()) {
        continue;
    }

    if (($fileinfo->getFilename() == 'cleanup.php') ||
    ($fileinfo->getFilename() == 'cleanup.log')) {
        continue;
    }

    $cacheFilesCounter++;

    if ($fileHoursOld < 24) {
        continue;
    }

    unlink($fileinfo->getFilename());
    $deleteCounter++;
}

include_once(APP_ROOT . "classes/cleanup-log.php");
$log = new cleanupLog($deleteCounter);
$log->writeLogEntry();

echo 'Patron cache file cleanup daemon. ' . $deleteCounter . ' of ' . $cacheFilesCounter . ' serialized data file(s) deleted.';