<h3>Digital Archives</h3>
<br>
<form id="vital-search-form" name="vital-search-form" onsubmit="window.location.href = 'http://<?php echo $config->getVitalServer() ?>/vital/access/manager/Repository?query='+document.getElementById('encore-search-input').value+'music&queryType=vitalDismax'; return false;">
    <fieldset class="bento-fieldset">
        <!-- Text input-->
        <input id="encore-search-input" name="encore-search-text" type="text" placeholder="Search Our Digital Repositories" required autofocus>
        <button type="submit" id="vital-search-button" class="search-button" name="vital-search-button">
            Search</button>
    </fieldset>
</form>

<br>
<a href="http://<?php echo $config->getVitalServer() ?>/vital/access/manager/Advanced" target="_self">Advanced Search</a>
