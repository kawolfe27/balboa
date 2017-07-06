<?php

session_start();

include_once($_SERVER['DOCUMENT_ROOT'] . '/classes/config.php');
$config = new config();

?>
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <?php include 'includes/head-link.php' ?>
    <title><?php echo $config->getInstitutionName() ?> | Sierra API List</title>
</head>
<body>
<?php include "includes/header.php" ?>
<div class="container well">
    <div class="page-header">
        <h1><a name="apiOverview">Sierra APIs <small>Exploring the possibilities</small></a></h1>
    </div>

    <div class="col-xs-12 col-md-9">
        <p>The <?php echo $config->getInstitutionName() ?> website is a showcase for the APIs avaialble with the Sierra Library System Platform. When you visit our pages you see a variety of ways in which you can create web content and "plug it in" to your Sierra system.</p>

        <p>Below is a list of the specific Sierra APIs that we've employed and exactly how we've implemented them on this site.</p>
        <p><b>Note!</b>  This is NOT a complete list of available APIs. It's only those we've actually employed on this site. You can also review our <a href="https://sierra-public.iii.com/iii/sierra-api/swagger/" target="_blank">complete documentation discussing the entire Sierra API suite.</a></p>

        <br>

        <!-- the patron API -->
        <table class="table table-striped table-hover">
            <tr>
                <td colspan="3">
                    <h2><a name="patronApi"><span class="glyphicon glyphicon-user"></span> The Patron API</a></h2>
                </td>
            </tr>

            <tr>
                <th>Method</th>
                <th>Read/Write?</th>
                <th>How Implemented on This Site</th>
            </tr>

            <tr>
                <td>validate</td>
                <td>read</td>
                <td>When the patron logs in as a patron, the API validates that the user credentials are valid in the Sierra database.</td>
            </tr>

            <tr>
                <td>find</td>
                <td>read</td>
                <td>Once a patron logs in, the API looks up their patron record and provides the ability to display the patron name and other information on pages throughout the site ("My Account" pages, for example).</td>
            </tr>

            <tr>
                <td>update</td>
                <td>write</td>
                <td>A logged in patron can change their PIN from the "My Account" page.  The Sierra API validates the format of the new PIN and (if in proper format) updates the patron record in the Sierra database with the new PIN number.</td>
            </tr>

            <tr>
                <td>checkouts</td>
                <td>read</td>
                <td>Patrons can access their "My Account" page and get a list of what they have checked out. The checkouts API call retrieves all the items checked out to the logged-in patron and provides a link to the associated item record.</td>
            </tr>

            <tr>
                <td>checkouts renewal</td>
                <td>write</td>
                <td>Patrons can renew any item or set of items from their list of checkouts. The Sierra API makes the renewal request against the Sierra database and the user is informed whether or not the renewal was successful.</td>
            </tr>

            <tr>
                <td>checkouts history</td>
                <td>read</td>
                <td>Patrons can get a current list of titles they've checked out in the past. The Sierra API provides the checkout history information which allows us to link to and display the bibliographic information.</td>
            </tr>

            <tr>
                <td>hold requests</td>
                <td>write</td>
                <td>Patrons (once logged in) can see a title in the carousel and place a hold request on it. The Polaris API places the
                    hold request in the Sierra database and provides information about whether or not the hold was placed successfully.</td>
            </tr>

            <tr>
                <td>holds</td>
                <td>read</td>
                <td>Patrons can get a current list of titles they have on hold. The API provides a list of all the holds related to the
                    specific patron.</td>
            </tr>

            <tr>
                <td>holds modify - cancel</td>
                <td>write</td>
                <td>From the list of their holds a patron can choose to cancel one or more holds.  The Sierra API makes the cancellation request and provides information about whether or not the hold was canceled in the Sierra database.</td>
            </tr>

            <tr>
                <td>holds modify - freeze/unfreeze</td>
                <td>write</td>
                <td>From the list of their holds a patron can choose to freeze an active hold (or UNfreeze an already frozen hold).  The Sierra API makes the request and provides information about whether or not the hold was frozen/unfrozen in the Sierra database.</td>
            </tr>

            <tr>
                <td>holds modify - change pickup location</td>
                <td>write</td>
                <td>A patron can change the pickup location of one or more holds in their list.  The Sierra API makes the location change request and provides information about whether or not the pickup location was changed in the Sierra database.</td>
            </tr>

            <tr>
                <td>fines</td>
                <td>read</td>
                <td>From their "My Account" page a patron can get a list of all the current fines and fees on their account including: descriptin, charges, partial payments, etc.</td>
            </tr>

            <!-- the bib API -->
            <tr>
                <td colspan="3">
                    <h2><a name="bibApi"><span class="glyphicon glyphicon-book"></span> The Bib API</a></h2>
                </td>
            </tr>
            <tr>
                <th>Method</th>
                <th>Read/Write?</th>
                <th>How Implemented on This Site</th>
            </tr>

            <tr>
                <td>bib query</td>
                <td>read</td>
                <td>The bibs query API call is used to generate the titles in the carousel. A Sierra staff user creates a review file and then generates the accompanying JSON. We give the JSON string to the query API which gives our app a list of bib ids and we build the carousel from there.</td>
            </tr>

            <tr>
                <td>bib id</td>
                <td>read</td>
                <td>This is the fundamental API call used throughout our integration. Any time we present bibiiographic information about a specific title (title, author, format, cover art, etc.) we used this API to extract the information from Sierra.  ANY MARC data is retrievable with the bib id API method.</td>
            </tr>

            <tr>
                <td>bib MARC</td>
                <td>read</td>
                <td>Yes, the bib id API returns selective MARC data; but the bib MARC API returns the <em>entire</em> MARC record data. Useful particularly on the full bib detail display where we offer the ability to view the full MARC record.</td>
            </tr>

            <!-- the item API -->
            <tr>
                <td colspan="3">
                    <h2><a name="itemApi"><span class="glyphicon glyphicon-barcode"></span> The Item API</a></h2>
                </td>
            </tr>
            <tr>
                <th>Method</th>
                <th>Read/Write?</th>
                <th>How Implemented on This Site</th>
            </tr>

            <tr>
                <td>items</td>
                <td>read</td>
                <td>The items API goes hand-in-hand with the bibs API. As you'd expect, when we look at the bibliographic detail of a record, we also use the items api to list all the linked item records. The items API is also used extensively throught the "My Account" pages in our implementation. In the case of checkouts or reading history the basic information we have about each entry is it's item ID. So when we get a list of items (patron checkouts for example) we then call the bib API to supplement the item information with bib data such as the item's title, author, etc.</td>
            </tr>

            <!-- the internal API -->
            <tr>
                <td colspan="3">
                    <h2><a name="metadataApi"><span class="glyphicon glyphicon-wrench"></span> The Internal - Metadata API</a></h2>
                </td>
            </tr>
            <tr>
                <th>Method</th>
                <th>Read/Write?</th>
                <th>How Implemented on This Site</th>
            </tr>

            <tr>
                <td>pickup locations</td>
                <td>read</td>
                <td>The pickup locations API provides a list of the library locations which have been identified in Sierra as holds pickup locations. The list comes into play whenever a patrons choose to place a hold or change hold pickup locations.</td>
            </tr>
        </table>
    </div>

    <div class="col-xs-0 col-md-3 visible-lg-* visible-md-* hidden-sm hidden-xs">
        <nav class="navbar" data-spy="affix">
            <ul class="nav nav-pills nav-stacked">
                <li class=""><a href="#apiOverview">Overview</a></li>
                <li class=""><a href="#patronApi">The Patron API</a></li>
                <li class=""><a href="#bibApi">The Bib API</a></li>
                <li class=""><a href="#itemApi">The Item API</a></li>
                <li class=""><a href="#metadataApi">The Metadata API</a></li>
            </ul>
        </nav>
    </div>
</div>

<?php include 'includes/footer.php' ?>
</body>
</html>