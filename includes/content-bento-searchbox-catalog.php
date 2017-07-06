<h3>Books/Catalog</h3>
<br>
<form method="get" action="https://<?php echo $config->getDB() ?>/search/" name="classic-search-form" id="classic-search-form" target="_self">
    <fieldset class="bento-fieldset">
        <select id="classic-search-type" name="searchtype">
            <option value="Y" selected="selected">Simple Search</option>
            <option value="X">Advanced Search</option>
            <option value="t">Title</option>
            <option value="s">Journal Title</option>
            <option value="a">Author</option>
            <option value="d">Subject</option>
            <option value="p">Course Professor/Instructor</option>
            <option value="r">Course Department/Name</option>
            <option value="c">Call Number</option>
            <option value="g">Government Document Number</option>
            <option value="i">Standard Number (ISBN, ISSN, etc.)</option>
            <option value="o">OCLC/Bibliographic Control Number</option>
        </select>
        <input id="classic-search-input" name="searcharg" size="20" maxlength="60" type="text"  placeholder="Browse the Catalog" required>
        <input name="SORT" value="D" type="hidden">
        <input type="submit" value="Search" class="search-button" id="classic-search-button">
    </fieldset>
</form>
<br>
<a href="https://<?php echo $config->getDB() ?>/search/X" target="_self">Advanced Search</a>  ·  <a href="https://<?php echo $config->getDB() ?>/patroninfo" target="_self">My Library Account</a>
