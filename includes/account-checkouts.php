<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

if (isset($_SESSION["itemsRenewed"])) {
    $alertMessage = $_SESSION["itemsRenewed"] . " item(s) renewed. <a href='#' class='more-prompt' data-toggle='tooltip' data-placement='right' title='There are conditions under which some or all of your items may not be renewed. For example, you may have fines and fees that block your renewals. In addition, your library places a limit on how many times a given type of item may be renewed. Check with library staff if you have concerns or questions.'>more...</a>";
    echo '<div class="alert alert-success" role="alert">' .
        '<a href="#" class="close" data-dismiss="alert">&times;</a>' .
        $alertMessage .
        '</div>';
    unset($_SESSION["itemsRenewed"]);
}

include($_SERVER['DOCUMENT_ROOT'] . '/classes/sierra-patron-checkouts-expanded.php');

$checkoutData = new sierraPatronCheckoutsExpanded($_SESSION['patronId']);

// serialize the data so we don't have to re-read all the API data when we re-sort the contents
$serialData = serialize($checkoutData);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/checkoutData." . session_id(), $serialData);

if ($checkoutData->getTotalCheckouts() == 0) {
    echo '<br>';
    echo 'You have nothing currently on loan from the ' . $config->getInstitutionName() . '.';
} else {
    echo '<div id="checkout-data">';
       include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/account-checkouts-rows.php");
    echo '</div>';
}
?>
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
