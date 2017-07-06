<h3>Quick Search - Books, Articles &amp; More</h3>
<br>
<form id="encore-search-form" name="encore-search-form" onsubmit="window.location.href = 'https://<?php echo $config->getPacServer() ?>/iii/encore/plus/C__S'+document.getElementById('encore-search-input').value; return false;">
    <fieldset class="bento-fieldset">
        <!-- Text input-->
        <input id="encore-search-input" name="encore-search-text" type="text" placeholder="Search Databases, Articles and Catalog" required autofocus>
        <button type="submit" id="encore-search-button" class="search-button" name="encore-search-button">
            Search</button>
    </fieldset>
</form>

<br>
<a href="https://<?php echo $config->getPacServer() ?>?lang=eng&suite=def&advancedSearch=true&searchString=" target="_self">Advanced Search</a>  ·  <a href="https://<?php echo $config->getPacServer() ?>/iii/encore/myaccount" target="_self">My Library Account</a>
