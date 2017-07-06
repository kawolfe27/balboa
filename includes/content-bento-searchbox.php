<input id="tab1" class="tab-button" type="radio" name="tabs" checked>
<label class="bento-label" for="tab1">Quick Search</label>

<input id="tab2" class="tab-button" type="radio" name="tabs">
<label class="bento-label" for="tab2">Books/Catalog</label>

<input id="tab3" class="tab-button" type="radio" name="tabs">
<label class="bento-label" for="tab3">Databases</label>

<input id="tab4" class="tab-button" type="radio" name="tabs">
<label class="bento-label" for="tab4">Digital Archives</label>

<div id="content1" class="bento-section">
    <?php include "includes/content-bento-searchbox-quick-search.php" ?>
</div>

<div id="content2" class="bento-section">
    <?php include "includes/content-bento-searchbox-catalog.php" ?>
</div>

<div id="content3" class="bento-section">
    <?php include "includes/content-bento-searchbox-databases.php" ?>
</div>

<div id="content4" class="bento-section">
    <?php include "includes/content-bento-searchbox-digital-archives.php" ?>
</div>
