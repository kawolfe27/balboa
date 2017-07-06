<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/ideas.php");
$ideas = new ideas();
?>
<div id="todoModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header my-modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">To Do List</h4>
            </div>
            <div class="modal-body">
                <form method="post" action="/includes/manage-todo.php" > <!-- onsubmit="window.location.assign('#close')" -->
                    <table class="table">
                        <tr>
                            <th></th>
                            <th>Description</th>
                        </tr>
                        <tr>
                            <td>
                                <button class="task-button" type="submit" name="addIdea"><span class="glyphicon glyphicon-plus"></span></button>
                            </td>
                            <td>
                                <input id="newIdea" name="newIdea" type="text" placeholder="describe your new idea" class="form-control input-md" autofocus>
                            </td>
                        </tr>

                        <?php for ($i = 0; $i < $ideas->getTotalIdeas(); ++$i) : ?>
                            <tr>
                                <td>
                                    <button class="task-button" type="submit" name="trashCan" value="<?php echo $i ?>"><span class="glyphicon glyphicon-trash"></span></button>
                                </td>
                                <td>
                                    <?php echo $ideas->getAnIdea($i) ?>
                                </td>
                            </tr>
                        <?php endfor; ?>

                    </table>
                </form>
            </div>
            <div class="modal-footer my-modal-footer">
                <div class="btn-group">
                     <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
