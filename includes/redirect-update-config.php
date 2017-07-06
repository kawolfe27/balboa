<?php

session_start();

// the post variable should be an array of hold id's and the value of the button that was selected
if (isset($_POST)) {
    include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
    $config = new config();

    $config->setInstitutionName($_POST['institutionName']);
    $config->setLogoFileName($_POST['logoFileName']);

    $config->setDB($_POST['db']);
    $config->setPacServer($_POST['pacServer']);
    $config->setIrServer($_POST['irServer']);
    $config->setVitalServer($_POST['vitalServer']);

    $config->setApiVer($_POST['apiVer']);
    $config->setApiKey($_POST['apiKey']);
    $config->setApiSecret($_POST['apiSecret']);

    $config->setContentCafeID($_POST['contentCafeID']);
    $config->setContentCafePassword($_POST['contentCafePassword']);

    $config->setCommentSendEmail($_POST['commentSendEmail']);
    $config->setCommentSender($_POST['commentSender']);
    $config->setSendTo($_POST['replyTo']);
    $config->setCommentRecipient($_POST['commentRecipient']);

    $config->setCarouselQueryFilename($_POST['carouselQueryFilename']);
    $config->setCarouselDataRefresh($_POST['carouselDataRefresh']);

    $config->setExcludeBibsWithoutISBN($_POST['excludeBibsWithoutISBN']);
    $config->setHyperlinkBibsToEncore($_POST['hyperlinkBibsToEncore']);

    $config->setDefaultHoldPickupLocationCode($_POST['defaultHoldPickupLocationCode']);


    $config->serializeConfigData();
}

header("location: /index.php");

/* FUNCTION DEFINITIONS (must exist OUTSIDE conditional statements or Php doesn't see them) */

