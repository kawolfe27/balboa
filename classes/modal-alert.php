<?php
include_once('classes/config.php');
class modalAlert
{
    protected $config;

    private $message = "Your message here.";

    public function __construct($messageText) {

        $this->config = new config();
        $this->message = $messageText;
    }

    public function displayAlert() {
        ?>
        <div class="modal-dialog" role="document">
            <div class="modal-content my-modal-content">
                <div class="modal-header my-modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><?php echo $this->config->getInstitutionName() ?></h4>
                </div>
                <div class="modal-body my-modal-body">
                    <p><?php echo $this->message ?></p>
                </div>
                <div class="modal-footer my-modal-footer">
                    <button type="button" class="btn center-block btn-primary" data-dismiss="modal">OK</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
        <?php
    }


}