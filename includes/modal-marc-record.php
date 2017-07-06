<div id="marcRecordModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header my-modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">MARC Record: <?php echo $bibData->getBibId() ?></h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-marc">
                    <tbody>
                <?php

                $marc_array = $bibData->getMarcRecord();

                // get the tag numbers for sorting
                $tags = array();
                foreach ($marc_array['fields'] as $thisTag) {
                    foreach ($thisTag as $tagNumber => $tagData) {
                        $tags[] = $tagNumber;
                    }
                }

                array_multisort($tags, SORT_ASC, $marc_array['fields']);

                echo '<tr>';
                echo '<td><span class="marc-tag">LDR</span></td><td></td><td>' . $marc_array['leader'] . '</td>';
                echo '</tr>';


                foreach ($marc_array['fields'] as $thisTag) {
                    foreach ($thisTag as $tagNumber => $tagData) {
                        echo '<tr>';

                        // display the tag number
                        echo '<td><span class="marc-tag">' . $tagNumber . '</span></td>';

                        // test to see if tagNumber represents an array
                        if (!is_array($tagData)) {
                            // if the tag is a fixed field we don't drill down.  we just display it
                            echo '<td></td><td>' . $tagData . '</td>';
                        } else {
                            // drill to subfield data}

                            // display the indicators (and show an underline in the place of an empty indicator position
                            echo '<td><span class="marc-indicator">';
                            echo (($tagData['ind1'] == ' ') ? "_" : $tagData['ind1']);
                            echo (($tagData['ind2'] == ' ') ? "_" : $tagData['ind2']);
                            echo '</span></td>';

                            // display the subfield data
                            echo '<td>';
                            foreach ($tagData['subfields'] as $thisSubfield) {
                                foreach ($thisSubfield as $subfieldCode => $subfieldData) {
                                    echo '<span class="marc-subfield">$' . $subfieldCode . '</span> ' . htmlentities($subfieldData);
                                }
                            }
                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                }
                ?>
                </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer my-modal-footer">
            <button type="submit" name="closeMarcDialog" class="btn btn-primary" data-dismiss="modal">OK</button>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->