<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Balboa University Library | Reading Suggestions</title>
    <?php include 'includes/head-link.php' ?>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well">

    <div class="page-header">
        <h1>What's Next?  <small>Let us help you choose your next great adventure!</small></h1>
    </div>
    <ul class="nav nav-tabs">
        <li class="active"><a href="#adult" data-toggle="tab" class="tabText"><span class="glyphicon glyphicon-knight glyphicon-tab"></span> Adult</a></li>
        <li><a href="#young-adult" data-toggle="tab" class="tabText"><span class="glyphicon glyphicon-cloud glyphicon-tab"></span> Young Adult</a></li>
        <li><a href="#children" data-toggle="tab" class="tabText"><span class="glyphicon glyphicon-picture glyphicon-tab"></span> Children</a></li>
        <li><a href="#largeprint" data-toggle="tab" class="tabText"><span class="glyphicon glyphicon-user glyphicon-tab"></span> Large Print</a></li>
    </ul>
    <div class="tab-content">
        <div id="adult" class="tab-pane fade in active">
            <h3>Westeros</h3>

            <div class="col-xs-6 col-md-3">
            <img class="img-responsive" src="images/westerosmap.png">
            </div>
            <div class="col-xs-6 col-md-9">
                <p>Westeros is a continent located in the far west of the known world. It is separated from the continent of Essos by a strip of water known as the Narrow Sea. Most of the action in Game of Thrones takes place in Westeros.</p>

                <p>Author of the series George R.R. Martin has stated that the continent of Westeros is roughly the same size as the real-life continent of South America.</p>

                <p>Almost the entire continent, barring only the lands in the furthest north beyond the Wall, is ruled by a single political entity known as the Seven Kingdoms, which holds fealty to the King of the Andals and the First Men, who sits on the Iron Throne in the city of King's Landing. The terms "Seven Kingdoms" and "Westeros" are normally used interchangeably.</p>

                <p>People or things from Westeros are referred to as "Westerosi".</p>
            </div>







        </div>

        <div id="young-adult" class="tab-pane fade">
            <h3>Meereen</h3>
            <p>Meereen is the northernmost and greatest of the three great city-states of the Bay of Dragons, north of Yunkai and Astapor. It is located at the mouth of the Skahazadhan River, which flows from its origins in Lhazar through the mountains separating Meereen and the rest of Slaver's Bay from the Red Waste. The Dothraki Sea lies to the north, beyond the river. The wealthiest residents live in pyramids.</p>
        </div>

        <div id="children" class="tab-pane fade">
            <h3>Braavos</h3>
            <p>Braavos is one of the Free Cities located to the east of Westeros. It is the northern-most, the richest, and arguably the most powerful of the Free Citie.

                <p>A giant statue known as the Titan of Braavos guards the harbor entrance to the city.</p>

                <p>The people of Braavos are known as Braavosi.</p>

        </div>
        <div id="largeprint" class="tab-pane fade">
            <h3>Qarth</h3>
            <p>Qarth is a great trading city located on the southern coast of <a href="/wiki/Essos" title="Essos">Essos</a>, on the straits linking the <a href="/wiki/Summer_Sea" title="Summer Sea">Summer Sea</a> to the <a href="/wiki/Jade_Sea" title="Jade Sea">Jade Sea</a>. Ships from <a href="/wiki/Westeros" title="Westeros">Westeros</a>, the <a href="/wiki/Free_Cities" title="Free Cities">Free Cities</a>, the <a href="/wiki/Summer_Islands" title="Summer Islands">Summer Islands</a>, and <a href="/wiki/Slaver%27s_Bay" title="Slaver's Bay" class="mw-redirect">Slaver's Bay</a> all pass through the Straits of Qarth on their way to the great nations and trading centers of the Further East, such as <a href="/wiki/Yi_Ti" title="Yi Ti">Yi Ti</a> and <a href="/wiki/Asshai" title="Asshai">Asshai</a>.</p>
            </p>Unlike most cities in Essos, Qarth has no fear of the Dothraki, as the vast and forbidding Red Waste divides it from the Dothraki sea.[1] Nor was Qarth ever conquered by the old Valyrian Freehold.[2] The Red Waste also separates Qarth from land-based travel with other parts of Essos, but it is a major port that conducts brisk maritime trade with merchants from Westeros to Asshai. Qarth seems to be located on an oasis, for despite being surrounded by desert, the inside of the city is tropical. </p>

        </div>
    </div>
</div>

<?php include 'includes/footer.php' ?>

</body>
</html>