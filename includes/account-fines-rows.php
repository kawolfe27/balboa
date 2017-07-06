<?php

// when we load this page as an include, it gets the session_start from the parent page.  When we load it with jQuery, it doesn't have an active session and we need it.

if (!isset($_SESSION)) {
    session_start();
}

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-finesandfees.php");

$serialData = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/finesData." . session_id());
$finesData = unserialize($serialData);

if (isset($_POST['sortfield'])) {
    $finesData->sortFines($_POST['sortfield']);
    // re-serialize the data now that we've re-sorted it.
    $serialData = serialize($finesData);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/finesData." . session_id(), $serialData);
}

$sortIcon = "glyphicon-sort-by-attributes";
if ($finesData->getSortDirection() == 'DESC') {
    $sortIcon = "glyphicon-sort-by-attributes-alt";
}

// an explanation popup if they click the "Pay Fines Now" button that this is just a proof of concept
include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/modal-pay-fines.php');


echo '<table id="fineTable" class="table table-striped table-hover table-responsive">';

// build the column header

echo '<tr>';

// checkboxes

echo '<th><input type="checkbox" class="selectAll" title="Select ALL fines/fees for payment"></th>';

// bibTitle

echo '<th><a href="#" id="bibTitle" class="fineColHead">Title';
if ($finesData->getSortField() == "bibTitle") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// transaction date

echo '<th><a href="#" id="assessedDate" class="fineColHead">Transaction Date';
if ($finesData->getSortField() == "assessedDate") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// charge type

echo '<th><a href="#" id="chargeType" class="fineColHead">Charge Type';
if ($finesData->getSortField() == "chargeType") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// Amount Charged (net charges item charge plus processing, etc.)

echo '<th style="text-align: right"><a href="#" id="netCharges" class="fineColHead">Amount Charged';
if ($finesData->getSortField() == "netCharges") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// amount paid

echo '<th style="text-align: right"><a href="#" id="paidAmount" class="fineColHead">Amount Paid';
if ($finesData->getSortField() == "paidAmount") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// balance due

echo '<th style="text-align: right"><a href="#" id="balanceDue" class="fineColHead">Balance Due';
if ($finesData->getSortField() == "balanceDue") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

echo '</tr>';

// initialize the math fields that will form the column totals
$ttlAmount = 0;
$ttlPaid = 0;
$ttlBalance = 0;

// COMPOSE THE DATA ROWS

for ($i = 0; $i < $finesData->getTotalFines(); ++$i) {

    // checkbox
    echo '<tr>';
    echo '   <td>';
    echo '   <input type="checkbox" class="selectOne" name="' . $finesData->getFineIdNumber($i) . '" title="Select this fine/fee for payment"';
    echo (in_array($finesData->getFineIdNumber($i),$_POST["checkedFineIds"])) ? 'checked' : '';
    echo '>';
    echo '   </td>';

    // title
    echo '   <td>';
    echo '<i><a href="';
    echo ($config->getHyperlinkBibsToEncore() == '1') ?
        $finesData->getEncoreDeepLink($i) . ' target="_blank"' :
        $finesData->getBibDetailLink($i) . '"';
    echo '>';
    echo $finesData->getAFine($i)["bibTitle"] . '</a></i>';
    echo '</td>';

    // transaction date
    echo '   <td>';
    echo $finesData->getAssessedDateFormatted($i);
    echo '</td>';
    echo '<td>';
    echo $finesData->getAFine($i)["chargeType"]['display'];
    echo '   </td>';
    echo '   <td align="right">';
    $ttlAmount += $finesData->getAFine($i)["netCharges"];
    echo money_format('$%i', $finesData->getAFine($i)["netCharges"]);
    echo '   </td>';
    echo '<td align="right">';
    $ttlPaid += $finesData->getAFine($i)["paidAmount"];
    echo money_format('$%i', $finesData->getAFine($i)["paidAmount"]);
    echo '</td>';
    echo '<td align="right">';
    $ttlBalance += $finesData->getAFine($i)["balanceDue"];
    echo money_format('$%i', $finesData->getAFine($i)["balanceDue"]);
    echo '</td>';
    echo '</tr>';
}

// totals
echo '<tr style="font-weight: bold; font-size: 1.25em;">';

echo '<td>';
echo '</td>';
echo '<td colspan="3">';
echo 'TOTALS:';
echo '</td>';
echo '<td style="text-align: right">';
echo money_format('$%i', $ttlAmount);
echo '</td>';
echo '<td style="text-align: right">';
echo money_format('$%i', $ttlPaid);
echo '</td>';

echo '<td style="text-align: right">';
echo money_format('$%i', $ttlBalance);
echo '</td>';

echo '</tr>';
echo '</table>';
echo '<br>';
echo '<button type="button" class="accountButton btn btn-primary disabled" data-toggle="modal" data-target="#payFinesModal"><span class="glyphicon glyphicon-credit-card"></span> Pay Fines Now</button>';
?>
<script>


    // resort when column header clicked
    $(function() {
        $(".fineColHead").click(function() {
            // get the ids of all checkmarked fines so we can re-check them once the rows are redrawn
            event.preventDefault();
            var checkedFineIDs = $("#fineTable input:checkbox:checked").map(function(){
                return $(this).attr("name");
            }).get(); // <----
            $('#fines-data').load('includes/account-fines-rows.php', {sortfield: $(this).attr('id'), checkedFineIds: checkedFineIDs});
        });
    });

    /* toggle checkboxes on a page when the user selects the "all" checkbox in the header */
    $('.selectAll').change(function() {
        var checkboxes = $("input[class='selectOne']:visible");
        checkboxes.prop('checked', this.checked);
    });

    /* enable/disable pay fine based on whether a checkbox is checked or not  */
    $("input.selectOne, input.selectAll").change(function() {
        toggleCheckboxes();
    });

    function toggleCheckboxes() {
        if ($("input.selectOne:visible").is(':checked')) {
            $(".accountButton:visible").removeClass("disabled").addClass("active");
        } else {
            $(".accountButton:visible").removeClass("active").addClass("disabled");
        }
    }

    // manually run this function every time the page loads so the checkboxes are set properly on a re-sort.
    toggleCheckboxes();

    // The form is valid if any of the visible checkboxes are checked
    function validateForm() {
        var checkboxes = $("input[class='selectOne']:visible");
        return checkboxes.is(':checked');
    }

    // auto-dismiss the alerts after so long
    $(".alert").delay(4000).slideUp(200, function() {
        $(this).alert('close');
    });
</script>
