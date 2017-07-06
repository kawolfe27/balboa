<div id="aboutModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <!-- the modal header -->

            <div class="modal-header my-modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">About Us</h4>
            </div>

            <div class="modal-body">

                <!-- the tabs -->

                <ul class="nav nav-tabs">
                    <li class="nav active"><a href="#aboutTheLibrary" class="tabText" data-toggle="tab">About the Library</a></li>
                    <li class="nav"><a href="#technical" class="tabText" data-toggle="tab">Technical</a></li>
                </ul>

                <!-- the library about content -->

                <div class="tab-content">
                    <div class="tab-pane fade in active" id="aboutTheLibrary">
                         <br>
                        <?php echo '<p>' . $config->getInstitutionName() . ' offers more than 1.8 million volumes, hundreds of research databases, computer access, laptops on loan, a multimedia collection, group study spaces, 24-hour access and library staff members who help researchers from around the world.</p>'; ?>
                    </div>

                    <!-- the technical content -->

                    <div id="technical" class="tab-pane fade">
                        <br>
                        <p>This page provides information relative to the web application software and is included here primarily for diagnostic purposes.</p>

                        <table class="table table-striped">
                            <tbody>
                            <tr>
                                <td>
                                    Institution Name
                                </td>
                                <td>
                                    <?php echo $config->getInstitutionName(); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Database Server
                                </td>
                                <td>
                                    <?php echo $config->getDB() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Encore Server
                                </td>
                                <td>
                                    <?php echo $config->getPacServer(); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    INN-Reach Server
                                </td>
                                <td>
                                    <?php echo $config->getIrServer(); ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Sierra API version
                                </td>
                                <td>
                                    <?php echo $config->getApiVer(); ?>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer my-modal-footer">
            <span style="color: black">20600000013391</span>
            <button type="submit" name="closeMarcDialog" class="btn btn-primary" data-dismiss="modal">OK</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->