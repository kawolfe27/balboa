<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-pickupLocations.php");
$pickupLocations = new sierraPickupLocations();
$pickupLocationsArray = $pickupLocations->getPickupLocationsArray();
?>

<div id="placeHoldModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header my-modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Hold Request Options</h4>
            </div>
            <div class="modal-body">

                <label for="selectPickupLocation">Pickup Location For Hold:</label>                &nbsp;&nbsp;
                <select id="selectPickupLocation" name="pickupLocation">
                    <?php
                    foreach ($pickupLocationsArray as $description => $code) {
                        echo '<option value="' . $code . '"';
                        if ($code == $config->getDefaultHoldPickupLocationCode()) {
                            echo ' selected="selected"';
                        }
                        echo '>' . $description  . '</option>';
                    }
                    ?>
                </select>
                <br><br>

                <label for="notWantedAfter">Not Wanted After (<i>Optional</i>):</label>
                <input type="date"
                       id="notWantedAfter"
                       name="notWantedAfter"
                       min="<?php echo date('Y-m-d',strtotime(date("Y-m-d", mktime()) . " + 1 day")); ?>">
            </div>
        </div>
        <div class="modal-footer my-modal-footer">
            <button type="submit" name="placeHold" class="btn btn-primary">OK</button>
            &nbsp;
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
