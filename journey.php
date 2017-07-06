<?php session_start() ?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Targaryen Library | Geography</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="css/targaryen.css">
    <link href="https://fonts.googleapis.com/css?family=Uncial+Antiqua" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Dekko" rel="stylesheet">
    <style></style>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well">
    <div class="jumbotron">
        <h1>Daenerys's Journey</h1>
        <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
                <li data-target="#carousel-example-generic" data-slide-to="1"></li>
                <li data-target="#carousel-example-generic" data-slide-to="2"></li>
                <li data-target="#carousel-example-generic" data-slide-to="3"></li>
                <li data-target="#carousel-example-generic" data-slide-to="4"></li>
                <li data-target="#carousel-example-generic" data-slide-to="5"></li>
                <li data-target="#carousel-example-generic" data-slide-to="6"></li>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">
                <div class="item active">
                    <img src="images/danydrogo.jpg" alt="..." class="slideshow">
                    <div class="carousel-caption">
                        Our Queen's journey to Westeros
                    </div>
                </div>
                <div class="item">
                    <img src="images/daenerys-dragon1.jpg" alt="..." class="slideshow">
                    <div class="carousel-caption">
                        Daenerys becomes the Mother of Dragons
                    </div>
                </div>
                <div class="item">
                    <img src="images/danyqarth2.png" alt="..." class="slideshow">
                    <div class="carousel-caption">
                        Daenerys journeys to Qarth to save her Khalasar
                    </div>
                </div>
                <div class="item">
                    <img src="images/danyunsullied3.jpg" alt="..." class="slideshow">
                    <div class="carousel-caption">
                        Daenerys frees the Unsullied Army, and they follow their new queen
                    </div>
                </div>
                <div class="item">
                    <img src="images/danymereen4.jpg" alt="..." class="slideshow">
                    <div class="carousel-caption">
                        Daenerys journeys to Mereen, frees the slaves, and rules over the city
                    </div>
                </div>
                <div class="item">
                    <img src="images/danysonsoftheharpy5.jpg" alt="..." class="slideshow">
                    <div class="carousel-caption">
                        During her rule of Mereen, Daenerys was forced to leave Mereen because of the Sons of the Harpy
                    </div>
                </div>
                <div class="item">
                    <img src="images/danysailing6.jpg" alt="..." class="slideshow">
                    <div class="carousel-caption">
                        After making alliances with the heir of the Iron Islands and obtaining the loyalty of a great Khalasar, Daenerys and her trusted advisors head to Westeros to take back her throne
                    </div>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <a class="left carousel-control controls" href="#carousel-example-generic" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control controls" href="#carousel-example-generic" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

</div>

<?php include "includes/footer.php" ?>
</body>
</html>