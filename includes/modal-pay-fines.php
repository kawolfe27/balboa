<div id="payFinesModal" class="modal fade" tabindex="-1" role="dialog">
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . "/classes/modal-alert.php";
    $aboutMessage = '
<p>The "Pay Fines Now" button would require additional coding.  In most cases you would accomplish online eCommerce by integrating with the API of a third party provider like Envisionware or Comprise.</p>';
    $aboutAlert = new modalAlert($aboutMessage);
    $aboutAlert->displayAlert();
    ?>
</div>
