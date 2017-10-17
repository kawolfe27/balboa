<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/modal-change-email.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/modal-delete-email.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/modal-change-phone.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/modal-delete-phone.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/modal-change-address.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/modal-delete-address.php");
?>
    <table class="table table-striped table-hover table-responsive">
        <tr>
            <td>Patron ID:</td>
            <td>
                <?php
                echo $thisPatron->getPatronID();
                echo '<br>';
                ?>
            </td>
        </tr>

        <tr>
            <td>Name:</td>
            <td>
                <?php
                foreach ($thisPatron->getNames() as $thisName) {
                    echo $thisName;
                    echo '<br><br>';
                }
                ?>

            </td>
            <td>

            </td>
        </tr>

        <tr>
            <td>Barcode:</td>
            <td>
                <?php
                foreach ($thisPatron->getBarcodes() as $thisBarcode) {
                    echo $thisBarcode;
                    echo '<br><br>';
                }
                ?>
            </td>
            <td>
            </td>
        </tr>
        <tr>
            <td>PIN:</td>
            <td>
                <?php echo $thisPatron->getPatronPin(); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="changepin.php" title="Click here to change your personal identification number.">Change your PIN</a>
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>Birthdate:</td>
            <td>
                <?php echo $thisPatron->getPatronBirthDateFormatted(); ?>
            </td>
            <td>

            </td>
        </tr>
        <tr>
            <td>Expiration Date:</td>
            <td>
                <?php
                echo $thisPatron->getPatronExpirationDateFormatted();
                echo '<br>';
                ?>
            </td>
        </tr>
        <tr>
            <td>Address:</td>
            <td>
                <?php
                foreach ($thisPatron->getPatronAddresses() as $key=>$thisAddress) {
                    $showEdit = true;
                    foreach ($thisAddress->lines as $thisLine) {
                        echo $thisLine;
                        if ($showEdit == true) {
                                echo '&nbsp; &nbsp; <a href="#" data-toggle="modal" data-target="#changeAddressModal" title="Edit this address" onclick="arrayElementNo=' . $key . ';" ' .  '><span class="glyphicon glyphicon-pencil"></span></a>';
                                echo '&nbsp; &nbsp; <a href="#" data-toggle="modal" data-target="#deleteAddressModal" title="Delete this address" onclick="arrayElementNo=' . $key . ';" ' . '><span class="glyphicon glyphicon-remove" style="color: lightcoral"></span></a>';
                            $showEdit = false;
                        }
                        echo '<br>';
                    }
                    echo '<br>';
                }
                ?>
                <button class="btn btn-success btn-xs" type="button" data-toggle="modal" data-target="#changeAddressModal" title="Add an address"><span class="glyphicon glyphicon-plus"></span>&nbsp; &nbsp;Add</button>
            </td>
        </tr>
        <tr>
            <td>Phone Number:</td>
            <td>
                <?php
                foreach ($thisPatron->getPhones() as $key=>$thisPhone) {
                   echo $thisPhone->number;
                   echo '&nbsp; &nbsp; <a href="#" data-toggle="modal" data-target="#changePhoneModal" title="Edit this phone number" onclick="arrayElementNo=' . $key . ';" ' .  '><span class="glyphicon glyphicon-pencil"></span></a>';
                   echo '&nbsp; &nbsp; <a href="#" data-toggle="modal" data-target="#deletePhoneModal" title="Delete this phone number" onclick="arrayElementNo=' . $key . ';" ' . '><span class="glyphicon glyphicon-remove" style="color: lightcoral"></span></a>';
                    echo '<br><br>';
                }
                ?>
                <button class="btn btn-success btn-xs" type="button" data-toggle="modal" data-target="#changePhoneModal" title="Add a new phone number"><span class="glyphicon glyphicon-plus"></span>&nbsp; &nbsp;Add</button>
            </td>
        </tr>
        <tr>
            <td>Email Address:</td>
            <td>
                <?php
                foreach ($thisPatron->getEmails() as $key=>$thisEmail) {
                    echo $thisEmail;
                    echo '&nbsp; &nbsp; <a href="#" data-toggle="modal" data-target="#changeEmailModal" title="Edit this email address" onclick="arrayElementNo=' . $key . ';" ' .  '><span class="glyphicon glyphicon-pencil"></span></a>';
                    echo '&nbsp; &nbsp; <a href="#" data-toggle="modal" data-target="#deleteEmailModal" title="Delete this email address" onclick="arrayElementNo=' . $key . ';" ' . '><span class="glyphicon glyphicon-remove" style="color: lightcoral"></span></a>';
                    echo '<br><br>';
                }
                ?>
                <button class="btn btn-success btn-xs" type="button" data-toggle="modal" data-target="#changeEmailModal" title="Add a new email address"><span class="glyphicon glyphicon-plus"></span>&nbsp; &nbsp;Add</button>
            </td>
        </tr>
    </table>
    <br>
