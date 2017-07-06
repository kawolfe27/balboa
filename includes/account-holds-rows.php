<?php

// when we load this page as an include, it gets the session_start from the parent page.  When we load it with jQuery, it doesn't have an active session and we need it.

if (!isset($_SESSION)) {
    session_start();
}

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-holds.php");

$serialData = file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/holdsData." . session_id());
$holdsData = unserialize($serialData);

if (isset($_POST['sortfield'])) {
    // sort the holds in the holds data object
    $holdsData->sortHolds($_POST['sortfield']);

    // re-serialize the data now that we've re-sorted it.
    $serialData = serialize($holdsData);
    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/store/patron/holdsData." . session_id(), $serialData);
}

$sortIcon = "glyphicon-sort-by-attributes";
if ($holdsData->getSortDirection() == 'DESC') {
    $sortIcon = "glyphicon-sort-by-attributes-alt";
}

echo '<form name="holds-form" method="POST" action="/includes/redirect-process-holds.php" onSubmit="return validateForm();">';

// the new location modal.  It brings up the "select location" control in a lightbox, but essentially it's
// another field on this form.  Its OK button, however is THE submit button for this form.
include_once($_SERVER['DOCUMENT_ROOT'] . '/includes/modal-new-location.php');

echo '<table id="holdsTable" class="table table-striped table-hover table-responsive">';

// BUILD THE COLUMN HEADINGS

echo '<tr>';

// checkboxes

echo '<th><input type="checkbox" class="selectAll" title="Select ALL holds"></th>';

// bibTitle

echo '<th><a href="#" id="bibTitle" class="holdColHead">Title';
if ($holdsData->getSortField() == "bibTitle") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// frozen

echo '<th><a href="#" id="frozen" class="holdColHead">Frozen?';
if ($holdsData->getSortField() == "frozen") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// not needed after date

echo '<th><a href="#" id="notNeededAfterDate" class="holdColHead">Not Needed After';
if ($holdsData->getSortField() == "notNeededAfterDate") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// pickup location

echo '<th><a href="#" id="pickupLocation" class="holdColHead">Pickup Location';
if ($holdsData->getSortField() == "pickupLocation") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

// hold priority

echo '<th style="text-align: right"><a href="#" id="priority" class="holdColHead">Place on Hold List';
if ($holdsData->getSortField() == "priority") {
    echo ' <span class="glyphicon ' . $sortIcon . '"></span>';
}
echo '</a></th>';

echo '</tr>';

// BUILD ROW CONTENT

for ($i = 0; $i < $holdsData->getTotalHolds(); ++$i) {
    /* If the priority is an ILL (priority is null), then we do not want to display it since it can't be edited. */
    if (!isset($holdsData->getAHold($i)["priority"])) {
        continue;
    }

    // figure out if this hold is frozen and set a variable to use below
    if ($holdsData->getAHold($i)["frozen"]) {
        $frozenHold="yes";
    } else {
        $frozenHold="no";
    }

    echo '<tr>';
    echo '   <td>';
    echo '   <input type="checkbox" class="selectOne" name="' . $holdsData->getAHoldIdNumber($i) . '" data-frozen="' . $frozenHold . '" title="Select this hold" ';
    echo (in_array($holdsData->getAHoldIdNumber($i),$_POST["checkedHoldIds"])) ? 'checked' : '' . '>';
    echo '   </td>';

    echo '   <td>';  // title

    // if the hold is an item level hold, display a "book" icon indicating thus
    if ($holdsData->getAHold($i)["recordType"] == 'i') {
        echo '<a href="#" data-toggle="tooltip" data-placement="auto" title="This is a COPY specific hold"><span class="glyphicon glyphicon-book item-specific-icon"></span></a>  ';
    }

    // if the holdis a VOLUME specific hood, display a "book" icon indicating it.
    if ($holdsData->getAHold($i)["recordType"] == 'j') {
        echo '<a href="#" data-toggle="tooltip" data-placement="auto" title="This is a VOLUME specific hold"><span class="glyphicon glyphicon-book volume-specific-icon"></span></a>  ';
    }


    echo '<i><a href="';

    // if the hyperlinked bib is to encore (according to our config setting) then make it so in the HTML.
    echo ($config->getHyperlinkBibsToEncore() == '1') ?
        $holdsData->getEncoreDeepLink($i) . ' target="_blank"' :
        $holdsData->getBibDetailLink($i) . '"';
    echo '>';

    echo $holdsData->getAHold($i)["bibTitle"] . '</a></i>';
    echo '</td>';
    echo '   <td>';  // frozen
    echo $frozenHold;
    echo '   </td>';
    echo '   <td>';  // Not Needed After
    echo $holdsData->getNotNeededAfterDateFormatted($i);
    echo '   </td>';
    echo '   <td>';
    echo $holdsData->getAHold($i)["pickupLocation"]["name"];
    echo '   </td>';
    echo '   <td align="middle">';
    echo $holdsData->getAHold($i)["priority"]+1; /* Value coming from API is off by 1 */
    echo ' of ';
    echo $holdsData->getAHold($i)["priorityQueueLength"];
    echo '   </td>';
    echo '</tr>';
}
echo '</table>';
echo '<br>';

// freeze button
echo '<button type="submit" name="freezeHold" class="accountButton btn btn-primary disabled"><span class="glyphicon glyphicon-pause"></span> Freeze Selected</button>';

echo '&nbsp';

// UNfreeze button
echo '<button type="submit" name="unFreezeHold" class="accountButton btn btn-primary disabled"><span class="glyphicon glyphicon-play"></span> Unfreeze Selected</button>';

echo '&nbsp';

// cancel button
echo '<button type="submit" name="cancelHold" class="accountButton btn btn-primary disabled"><span class="glyphicon glyphicon-remove"></span> Cancel Selected</button>';

echo '&nbsp';

// change pickup location button
echo '<button type="button" name="locationModal" data-target="#newLocationModal" data-backdrop="static" class="accountButton btn btn-primary disabled"><span class="glyphicon glyphicon-home"></span> Change Pickup Location</button>';

echo '</form>';
?>
<script>
    // resort when column header clicked
    $(function() {
        $(".holdColHead").click(function() {
            // get the ids of all checkmarked holds so we can re-check them once the rows are redrawn
            event.preventDefault();
            var checkedHoldIDs = $("#holdsTable input:checkbox:checked").map(function(){
                return $(this).attr("name");
            }).get(); // <----

            // reload this page with post parameters:  new sort field name and the ids of all the holds that were checked
            $('#hold-data').load('includes/account-holds-rows.php', {sortfield: $(this).attr('id'), checkedHoldIds: checkedHoldIDs});
        });
    });

    /* toggle checkboxes on a page when the user selects the "all" checkbox in the header */
    $('.selectAll').change(function() {
        var checkboxes = $("input[class='selectOne']:visible");
        checkboxes.prop('checked', this.checked);
    });

    /* enable/disable account buttons based on whether any checkbox is checked or not  */
    $("input.selectOne, input.selectAll").change(function() {
        // enable the buttons if any of the checkboxes are checked
        toggleCheckboxes();
    });

    function toggleCheckboxes() {
        if ($("input.selectOne:visible").is(':checked')) {
            // at least one checkbox is checked, so we enable ALL the buttons
            $(".accountButton:visible").removeClass("disabled").addClass("active");
            // the change location button is a special condition
            $("[name=locationModal]").attr('data-toggle','modal');

            // now disable the freeze button if we've checked an already frozen hold
            if ($("input[data-frozen='yes']").is(':checked')) {
                $("[name=freezeHold]").removeClass("active").addClass("disabled");
            }
            // now disable the UNfreeze button if we've checked an unfrozen hold
            if ($("input[data-frozen='no']").is(':checked')) {
                $("[name=unFreezeHold]").removeClass("active").addClass("disabled");
            }
        } else {
            // no checkboxes are checked so it's easy.  we just disable all the buttons.
            $(".accountButton:visible").removeClass("active").addClass("disabled");
            $("[name=locationModal]").removeAttribute('data-toggle');
        }
    }

    // The form is valid if any of the visible checkboxes are checked
    function validateForm() {
        var checkboxes = $("input[class='selectOne']:visible");
        return checkboxes.is(':checked');
    }

    $(".alert").delay(4000).slideUp(200, function() {
        $(this).alert('close');
    });

    // change the cursor to indicate we're busy when a button is clicked
    $("button").click( function() {
        if (!$(this).hasClass('disabled')) {
            $("html, body, .btn").css("cursor", "progress");
        }
    });

    // run this function every time the page loads so the checkboxes are set correctly after a re-sort.
    toggleCheckboxes();

</script>
