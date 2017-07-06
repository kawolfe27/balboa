<?php session_start() ?>

<?php

include "classes/ideas.php";
$ideas = new ideas();

// deal with form submission if that's how we got here
if (isset($_POST['trashCan'])) {
    $ideas->deleteAnIdea($_POST['trashCan']);
}

if(isset($_POST['newIdea'])) {
    if ($_POST['newIdea'] != "") {
        $ideas->addAnIdea($_POST['newIdea']);
    }
}
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>
    <title><?php echo $config->getInstitutionName() ?> | To Do List</title>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well">
    <div class="page-header">
        <h1>To Do List<small>  tasks, dreams, and ideas</small></h1>
    </div>
    <form method="post">
        <table class="table table-striped table-hover">
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
                        <?php echo htmlspecialchars($ideas->getAnIdea($i)) ?>
                    </td>
                </tr>
            <?php endfor; ?>

        </table>
    </form>
</div>

<?php include 'includes/footer.php' ?>
</body>
</html>