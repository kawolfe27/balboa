<h3>Databases</h3>
<br>
<form id="DB-search-form" name="DB-search-form" method="get" action="https://<?php echo $config->getDB() ?>/search/?searchtype=m">
    <fieldset class="bento-fieldset">
        <!-- Text input-->
        Select Databases by <b>Subject Area</b>:
        <br>
        <br>
        <input name="searchtype" value="m" hidden>
        <select name="searcharg">
            <option value="african american african studies" selected="">African American/African Studies</option>
            <option value="agriculture">Agriculture</option>
            <!-- <option value="anthropology">Anthropology</option> -->
            <option value="art and architecture">Art and Architecture</option>
            <option value="arts">Arts</option>
            <option value="astronomy">Astronomy</option>
            <option value="biography">Biography</option>
            <option value="biology">Biology</option>
            <option value="book reviews">Book Reviews</option>
            <option value="business and economics">Business and Economics</option>
            <option value="chemistry and chemical engineering">Chemistry and Chemical Engineering</option>
            <option value="communications journalism">Communications/Journalism</option>
            <option value="computer science">Computer Science</option>
            <option value="current events">Current Events</option>
            <option value="dictionaries, directories, and encyclopedias">Dictionaries, Directories, and Encyclopedias</option>
            <option value="education">Education</option>
            <option value="engineering">Engineering</option>
            <option value="ethnic studies">Ethnic Studies</option>
            <!-- <option value="geography (human)">Geography (Human)</option> -->
            <option value="geography (physical)">Geography (Physical)</option>
            <option value="geology">Geology</option>
            <option value="government documents">Government Documents</option>
            <option value="history">History</option>
            <option value="human ecology">Human Ecology</option>
            <option value="international studies">International Studies</option>
            <option value="language and literature">Language and Literature</option>
            <option value="law">Law</option>
            <!-- <option value="library science">Library Science</option> -->
            <option value="mathematics and statistical methods">Mathematics and Statistical Methods</option>
            <option value="medicine and health">Medicine and Health</option>
            <option value="multidisciplinary studies">Multidisciplinary Studies</option>
            <option value="multimedia">Multimedia</option>
            <option value="music">Music</option>
            <option value="newspapers">Newspapers</option>
            <option value="performing arts (including theatre)">Performing Arts (Including Theatre)</option>
            <option value="philosophy">Philosophy</option>
            <option value="physics">Physics</option>
            <option value="politics and political science">Politics and Political Science</option>
            <!-- <option value="psychology">Psychology</option> -->
            <option value="religion">Religion</option>
            <option value="science">Science</option>
            <!-- <option value="sociology and social work">Sociology and Social Work</option>
            <option value="sports and leisure studies">Sports and Leisure Studies</option> -->
            <option value="statistics">Statistics</option>
            <option value="technology">Technology</option>
            <!-- <option value="veterinary medicine">Veterinary Medicine</option> -->
            <option value="women studies gender">Women Studies Gender</option>

        </select>
        <button type="submit" id="db-subject-browse-button" class="search-button">
            Browse</button>
        <br>
        <br>
        <div class="db-alpha-list">
            Browse by <b>Database</b> Titles:
            <br>
            <br>
            <ul>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?a">A</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?b">B</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?c">C</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?d">D</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?e">E</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?f">F</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?g">G</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?h">H</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?i">I</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?j">J</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?k">K</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?l">L</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?m">M</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?n">N</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?o">O</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?p">P</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?q">Q</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?r">R</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?s">S</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?t">T</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?u">U</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?v">V</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?w">W</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?x">X</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?y">Y</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?z">Z</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/y?0">#</a></li>
            </ul>

            <br>
            <br>
            Browse by <b>Journal</b> Titles:
            <br>
            <br>
            <ul>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?a">A</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?b">B</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?c">C</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?d">D</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?e">E</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?f">F</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?g">G</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?h">H</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?i">I</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?j">J</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?k">K</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?l">L</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?m">M</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?n">N</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?o">O</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?p">P</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?q">Q</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?r">R</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?s">S</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?t">T</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?u">U</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?v">V</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?w">W</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?x">X</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?y">Y</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?z">Z</a></li>
                <li><a href="https://<?php echo $config->getDB() ?>/search/s?0">#</a></li>
            </ul>
        </div>
    </fieldset>
</form>