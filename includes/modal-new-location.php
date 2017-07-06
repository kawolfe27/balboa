<?php
    include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-pickupLocations.php");
    $pickupLocations = new sierraPickupLocations();
    $pickupLocationsArray = $pickupLocations->getPickupLocationsArray();
?>

<div id="newLocationModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header my-modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $config->getInstitutionName() ?></h4>
            </div>
            <div class="modal-body">

                New Pickup Location For Selected Hold(s):
                &nbsp;&nbsp;
                <select id="selectNewLocation" name="newLocation">
                    <?php
                    foreach ($pickupLocationsArray as $description => $code) {
                        echo '<option value="' . $code  . '">' . $description  . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="modal-footer my-modal-footer">
            <button type="submit" name="changePickup" class="btn btn-primary">OK</button>
            &nbsp;
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
