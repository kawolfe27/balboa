<?php

// when we load this page as an include, it gets the session_start from the parent page.  When we load it with jQuery, it doesn't have an active session and we need it.

if (!isset($_SESSION)) {
    session_start();
}
include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-readinghistory.php");

$serialData = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/historyData." . session_id());
$historyData = unserialize($serialData);

if (isset($_POST['sortfield'])) {
    $historyData->sortHistory($_POST['sortfield']);
}

// re-serialize the data now that we've re-sorted it.
$serialData = serialize($historyData);
file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/historyData." . session_id(), $serialData);

$sortIcon = "glyphicon-sort-by-attributes";
if ($historyData->getSortDirection() == 'DESC') {
    $sortIcon = "glyphicon-sort-by-attributes-alt";
}

// HERE'S A HEADER FOR WHEN THE LIST IS PRINTED
echo '<div id="print-header">';
echo '<img src="/images/balboa-logo.png" />';
echo '<h1>' . $config->getInstitutionName() . '</h1>';
echo '<h2>My Reading History</h2>';
echo '<br>';
echo '</div>';

echo '<table class="table table-striped table-hover table-responsive">';

// BUILD THE COLUMN HEADER

echo '<tr>';

// format

echo '<th> <a href="#" id="itemType" class="historyColHead">Format';
if ($historyData->getSortField() == "itemType") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// bibTitle

echo '<th><a href="#" id="bibTitle" class="historyColHead">Title';
if ($historyData->getSortField() == "bibTitle") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// author

echo '<th><a href="#" id="bibAuthor" class="historyColHead">Author';
if ($historyData->getSortField() == "bibAuthor") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// checkout date

echo '<th><a href="#" id="outDate" class="historyColHead">Checkout Date';
if ($historyData->getSortField() == "outDate") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

echo '</tr>';

// BUILD THE ROW DATA

for ($i = 0; $i < $historyData->getTotalHistory(); ++$i) {
    echo '<tr>';
    echo '<td>'; // format
    echo $historyData->getAHistory($i)["itemType"];
    echo '</td>';

    // title

    echo '   <td>';
    echo '<i><a href="';
    echo ($config->getHyperlinkBibsToEncore() == '1') ?
                $historyData->getEncoreDeepLink($i) . ' target="_blank"' :
                $historyData->getBibDetailLink($i) . '"';
    echo '>';
    echo $historyData->getAHistory($i)["bibTitle"] . '</a></i>';
    echo '</td>';

    // author

    echo '   <td>';
    echo '<a href="' . $historyData->getEncoreDeepLinkAuthor($i) . '" target="_blank">' . $historyData->getAHistory($i)["bibAuthor"] . '</a>';
    echo '   </td>';

    // checkout date

    echo '<td>';
    echo $historyData->getCheckoutDateFormatted($i);
    echo '   </td>';
    echo '</tr>';
}
echo '</table>';

// BUTTONS

// print button
echo '<button type="button" name="printHistory" class="accountButton btn btn-primary" onclick="window.print();"><span class="glyphicon glyphicon-print"></span> Print</button>';

echo '&nbsp';

// download button
echo '<button type="button" name="downloadHistory" class="accountButton btn btn-primary" onclick="location.href=\'/downloadreadinghistory.php\';"><span class="glyphicon glyphicon-download"></span> Download</button>';

?>
<script>
    // resort when column header clicked
    $(function() {
        $(".historyColHead").click(function() {
            $('#history-data').load('includes/account-readinghistory-rows.php', {'sortfield': $(this).attr('id')});
        });
    });
</script>
