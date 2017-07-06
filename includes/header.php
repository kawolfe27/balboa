<?php

// this function facilitates highlighting the menu option of the active page in the navbar.
function echoActiveClassIfRequestMatches($requestUri)
{
    $current_file_name = basename($_SERVER['REQUEST_URI'], ".php");
    if ($current_file_name == $requestUri)
        echo 'class="active"';
}

// modals
include_once($_SERVER['DOCUMENT_ROOT'] . "/includes/modal-about.php");

if (basename($_SERVER['PHP_SELF']) != 'todo.php') {
    include_once "includes/modal-todo.php";
}
?>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#balboa-menu" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="#" data-toggle="modal" data-backdrop="static" data-target="#todoModal"><img id="img-logo" src="images/library-logo.png"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="balboa-menu">
            <form class="navbar-form navbar-left visible-lg-* visible-md-* hidden-sm hidden-xs">
                <div class="form-group">
                    <input id="searchTerm" type="text" class="form-control" placeholder="Search the Library">
                </div>
                <button type="submit" class="btn btn-default" onclick="searchEncore()"><span class="glyphicon glyphicon-search"></span></button>
            </form>
            <ul class="nav navbar-nav navbar-right">
                <li <?=echoActiveClassIfRequestMatches("index")?>><a href="index.php"><span class="glyphicon glyphicon-home"></span> Home</a>

                <!-- Research services drop down -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Research Services<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="https://library.ucr.edu/research-services/research-fundamentals" target="_blank">Research Fundamentals</a></li>
                        <li><a href="https://library.ucr.edu/research-services/databases" target="_blank">Databases</a></li>
                        <li><a href="https://library.ucr.edu/research-services/subject-guides" target="_blank">Subject Guides</a></li>
                        <li><a href="http://library.ucr.edu/research-services/managing-your-data" target="_blank">Managing Your Data</a></li>
                    </ul>
                </li>

                <!-- Collections drop down -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Collections<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="http://library.ucr.edu/collections/notable-collections" target="_blank">Notable Collections</a></li>
                        <li><a href="http://library.ucr.edu/collections/collecting-areas/arts-humanities" target="_blank">Collecting Areas</a></li>
                        <li><a href="http://library.ucr.edu/collections/suggest-a-purchase" target="_blank">Suggest A Purchase</a></li>
                    </ul>
                </li>

                <!-- Instructional support drop down -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Instructional Support<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="http://library.ucr.edu/instructional-support/library-instruction-sessions" target="_blank">Library Instruction Sessions</a></li>
                        <li><a href="http://library.ucr.edu/instructional-support/tours-and-orientations" target="_blank">Tours and Orientation</a></li>
                        <li><a href="http://library.ucr.edu/instructional-support/put-materials-on-course-reserves" target="_blank">Put Materials On Course Reserves</a></li>
                        <li><a href="http://library.ucr.edu/instructional-support/copyright-teaching" target="_blank">Copyright & Teaching</a></li>
                    </ul>
                </li>

                <!-- help drop down -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Help<span class="caret"></span></a>
                    <ul class="dropdown-menu" role="menu">
                        <li><a href="#" data-toggle="modal" data-target="#aboutModal">About</a></li>
                        <li <?=echoActiveClassIfRequestMatches("contact")?>><a href="contact.php">Contact Us</a></li>
                        <li <?=echoActiveClassIfRequestMatches("about-api")?>><a href="about-api.php">Sierra APIs</a></li>
                    </ul>
                </li>

                <?php if (isset($_SESSION['barcode'])) {
                    echo '<li class="dropdown">';
                    echo '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">' . $_SESSION["patronName"] . '<span class="caret"></span></a>';
                    echo '<ul class="dropdown-menu" role="menu">';
                    echo '<li' . echoActiveClassIfRequestMatches("myaccount") . '><a href="account.php">My Account</a></li>';
                    echo '<li role="separator" class="divider"></li>';
                    echo '<li' . echoActiveClassIfRequestMatches("logout") . '><a href="logout.php?logout=true">Logout</a></li>';
                    echo '</ul>';
                    echo '</li>';
                } else {
                    echo '<li ';
                    echoActiveClassIfRequestMatches("login");
                    echo '><a href="login.php"><span class="glyphicon glyphicon-user"></span> Login</a></li>';
                }
                ?>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>

<!-- javascript to support the search tool in the nav bar -->
<script>
    function searchEncore() {
        var $encoreUrl='http://<?php echo $config->getPacServer(); ?>/iii/encore/plus/C__S'+document.getElementById('searchTerm').value;
        window.open($encoreUrl);
    }
</script>