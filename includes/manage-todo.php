<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ideas.php");
$ideas = new ideas();

// deal with form submission if that's how we got here
if (isset($_POST['trashCan'])) {
    $ideas->deleteAnIdea($_POST['trashCan']);
}

if(isset($_POST['newIdea'])) {
    if ($_POST['newIdea'] != "") {
        $ideas->addAnIdea(htmlspecialchars($_POST['newIdea']));
    }
}
$referer = $_SERVER['HTTP_REFERER'];
header("location:$referer");