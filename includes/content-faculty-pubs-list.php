<div class="main-page-content-block">

    <span class="main-page-content-header"><span class="glyphicon glyphicon-pencil"></span>  Faculty Publications</span>
    <table class="table table-striped table-hover table-responsive">

        <?php
        if (count($carouselBibs->carouselBibArray) == 0) {
            echo "No results.";
        } else {
            foreach ($carouselBibs->carouselBibArray as $thisBib) {
                echo '<tr>';
                echo '<td>';
                echo '<a href="/bibdetail.php?bibid=' . $thisBib["bibId"] . '">';
                echo '<i>' . $thisBib["bibTitle"] . '</i>';
                if (!$thisBib["bibAuthor"] == "") {
                    echo ' by '. $thisBib["bibAuthor"];
                }
                echo '</a>';
                echo '</td>';
                echo '</tr>';
            }
        }
        ?>
    </table>
</div>
