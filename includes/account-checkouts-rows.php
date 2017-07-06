<?php

// when we load this page as an include, it gets the session_start from the parent page.  When we load it with jQuery, it doesn't have an active session and we need it.
if (!isset($_SESSION)) {
    session_start();
}

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-patron-checkouts-expanded.php");

$serialData = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/checkoutData." . session_id());
$checkoutData = unserialize($serialData);

if (isset($_POST['sortfield'])) {
    $checkoutData->sortCheckouts($_POST['sortfield']);

    // re-serialize the data now that we've re-sorted it.
    $serialData = serialize($checkoutData);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/checkoutData." . session_id(), $serialData);
}

$sortIcon = "glyphicon-sort-by-attributes";
if ($checkoutData->getSortDirection() == 'DESC') {
    $sortIcon = "glyphicon-sort-by-attributes-alt";
}

echo '<form name="checkout-form" method="POST" action="renewcheckouts.php" onSubmit="return validateForm();">';
echo '<table id="checkoutsTable" class="table table-striped table-hover table-responsive">';

// BUILD THE COLUMN HEADER

echo '<tr>';

// checkboxes

echo '    <th><input type="checkbox" class="selectAll" title="Select ALL checkouts for renewal"></th>';

// call number

echo '<th><a href="#" id="callNumber" class="ckoColHead">Call Number';
if ($checkoutData->getSortField() == "callNumber") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// format

echo '<th><a href="#" id="itemType" class="ckoColHead">Format';
if ($checkoutData->getSortField() == "itemType") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// bibTitle

echo '<th><a href="#" id="bibTitle" class="ckoColHead">Title';
if ($checkoutData->getSortField() == "bibTitle") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// due date

echo '<th style="width: 120px"><a href="#" id="dueDate" class="ckoColHead">Due Date';
if ($checkoutData->getSortField() == "dueDate") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// times renewed

echo '<th><a href="#" id="numberOfRenewals" class="ckoColHead">Times Renewed';
if ($checkoutData->getSortField() == "numberOfRenewals") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

echo '</tr>';

// BUILD THE ROWS OF CONTENT

for ($i = 0; $i < $checkoutData->getTotalCheckouts(); ++$i) {
    echo '<tr>';
    echo '   <td>';
    echo '   <input type="checkbox" class="selectOne" name="' . $checkoutData->getCheckoutIdNumber($i) . '" title="Select this checkout for renewal " ';
    echo (in_array($checkoutData->getCheckoutIdNumber($i),$_POST["checkedCheckoutIds"])) ? 'checked' : '';
    echo '>';
    echo '   </td>';
    echo '   <td>';  // call number
    echo (array_key_exists("callNumber", $checkoutData->getACheckout($i))) ? $checkoutData->getACheckout($i)["callNumber"] : '';
    echo '   </td>';

    echo '   <td>'; // item type
    echo $checkoutData->getItemType($i);
    echo '   </td>';

    echo '   <td>';  // title
    echo '<i><a href="';
    echo ($config->getHyperlinkBibsToEncore() == '1') ?
        $checkoutData->getEncoreDeepLink($i) . ' target="_blank"' :
        $checkoutData->getBibDetailLink($i) . '"';
    echo '>';
    echo $checkoutData->getACheckout($i)["bibTitle"] . '</a></i>';
    echo '</td>';

    if ($checkoutData->isOverdue($i)) {
        echo '   <td class="overdue">';
    } else {
        echo '   <td>';  // due date
    }
    echo $checkoutData->getDueDateFormatted($i);
    echo '   </td>';
    echo '   <td class="centered">';
    echo $checkoutData->getACheckout($i)["numberOfRenewals"];
    echo '   </td>';
    echo '</tr>';
}
echo '</table>';
echo '<br>';
echo '<button type="submit" class="accountButton btn btn-primary disabled"><span class="glyphicon glyphicon-repeat"></span> Renew Selected</button>';
echo '</form>';
?>

<script>
    // resort when column header clicked
    $(function() {
        $(".ckoColHead").click(function() {
            // get the ids of all checkmarked checkouts so we can re-check them once the rows are redrawn
            event.preventDefault();
            var checkedCheckoutIDs = $("#checkoutsTable input:checkbox:checked").map(function(){
                return $(this).attr("name");
            }).get(); // <----

            $('#checkout-data').load('includes/account-checkouts-rows.php', {sortfield: $(this).attr('id'), checkedCheckoutIds: checkedCheckoutIDs});
        });
    });

    /* toggle checkboxes on a page when the user selects the "all" checkbox in the header */
    $('.selectAll').change(function() {
        var checkboxes = $("input[class='selectOne']:visible");
        checkboxes.prop('checked', this.checked);
    });

    /* enable/disable renew button based on whether a checkbox is checked or not */
    $("input.selectOne, input.selectAll").change(function() {
            toggleCheckboxes();
    });

    // The form is valid if any of the visible checkboxes are checked
    function validateForm() {
        var checkboxes = $("input[class='selectOne']:visible");
        return checkboxes.is(':checked');
    }

    function toggleCheckboxes() {
        if ($("input.selectOne:visible").is(':checked')) {
            $(".accountButton:visible").removeClass("disabled").addClass("active");
        } else {
            $(".accountButton:visible").removeClass("active").addClass("disabled");
        }
    }

    // actually invoke this function when the page is loaded so the checkboxes get enabled/disabled after a re-sort.
    toggleCheckboxes();

    // auto-dismiss the alerts after so long
    $(".alert").delay(4000).slideUp(200, function() {
        $(this).alert('close');
    });

    // change the cursor to indicate we're busy when a button is clicked
    $("button").click( function() {
        if (!$(this).hasClass('disabled')) {
            $("html, body, .btn").css("cursor", "progress");
        }
    });
</script>
