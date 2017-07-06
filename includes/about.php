<div id="aboutModal" class="modal fade" tabindex="-1" role="dialog">
  <?php include_once "classes/modal-alert.php";
  $aboutMessage =
      '<p>' . config::INSTITUTIONNAME . ' offers more than 1.8 million volumes, hundreds of research databases, computer access, laptops on loan, a multimedia collection, group study spaces, 24-hour access and library staff members who help researchers from around the world.</p>';
  $aboutAlert = new modalAlert($aboutMessage);
  $aboutAlert->displayAlert();
    ?>
</div>

