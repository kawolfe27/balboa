<?php
// this page is invoked when the user clicks the "refresh" button on the index page carousel. we delete
// the cached data file forcing the software to recompile the carousel data once it goes to draw the
// carousel on the index page.
include_once "/root-config.php";

if (isset($_GET['refresh'])) {
    unlink($_SERVER['DOCUMENT_ROOT'] . '/store/bib/bib.dat');
    header("Location: " . "/index.php");
}