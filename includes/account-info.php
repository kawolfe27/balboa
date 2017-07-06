<table class="table table-striped table-hover table-responsive">
    <tr>
        <td>Name:</td>
        <td>
            <?php echo $thisPatron->getPatronName(); ?>
        </td>
        <td>

        </td>
    </tr>

    <tr>
        <td>Barcode:</td>
        <td>
            <?php
            for ($i = 0; $i < $thisPatron->getTotalBarcodes(); ++$i) {
                echo $thisPatron->getPatronBarcode($i);
                echo '<br>';
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
        <td>Address:</td>
        <td>
            <?php
            for ($i = 0; $i < sizeof($thisPatron->getPatronAddressLines()->lines); ++$i) {
                echo $thisPatron->getPatronAddressLines()->lines[$i];
                echo '<br>';
            }
            ?>
        </td>
        <td>

        </td>
    </tr>
    <tr>
        <td>Phone Number:</td>
        <td>
            <?php
            for ($i = 0; $i < $thisPatron->getTotalPhoneNumbers(); ++$i) {
                echo $thisPatron->formattedPhoneNumber($i);
                echo '<br>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>Email Address:</td>
        <td>
            <?php
            for ($i = 0; $i < $thisPatron->getTotalEmails(); ++$i) {
                echo $thisPatron->getPatronEmail($i);
                echo '<br>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <td>Patron ID:</td>
        <td>
            <?php
            echo $thisPatron->getPatronID();
                echo '<br>';
            ?>
        </td>
    </tr>
</table>