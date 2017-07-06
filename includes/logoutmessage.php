<div id="logOutModal" class="modal fade" tabindex="-1" role="dialog">
    <?php include_once "classes/modal-alert.php";
    $logoutMessage = "You have successfully logged out.  Come back again soon!";
    $logoutAlert = new modalAlert($logoutMessage);
    $logoutAlert->displayAlert();

    ?>
</div><!-- /.modal -->
