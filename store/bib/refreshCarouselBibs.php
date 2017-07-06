<?php
/*
 * this script is intended for use by a CRON job. It deletes the old bib cache file of serialized data
 * and recreates it with fresh data.
 */

// we have to hard-code the value of APP_ROOT because this is run from the php command line via a CRON job.  We can't
// use anything like __DIR__ or $SERVER

define("APP_ROOT","/home2/polarisd/public_html/wolfe/balboa/");

// delete the old cache file
if (file_exists(APP_ROOT . 'store/bib/bib.dat')){
    unlink(APP_ROOT . 'store/bib/bib.dat');
}

// instantiate an object of carousel bib data
// when the object doesn't find a bib.dat file, it will get fresh data from the APIs and
// then serialize a new cache file.

$carouselQuery = file_get_contents(APP_ROOT . 'json/facpubs.json');
include_once(APP_ROOT.'classes/carousel-bibs.php');
$carouselBibs = new carouselBibs($carouselQuery);


echo 'Carousel bib data refreshed successfully.';