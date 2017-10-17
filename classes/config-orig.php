<?php

class configOrig
{
    const INSTITUTIONNAME = 'Balboa University Library'; /* e.g.:  Balboa University Library */
    const DB = 'sierra-academic.iii.com';
    const PACSERVER = 'encore-academic.iii.com';
    const IRSERVER = 'encorecalstate.iii.com';
    const VITALSERVER = 'vital-share.iii.com:8080';
    const APIVER = '3';
    const APIKEY = 'zIK+9ysnxp8WmcQe1eWxf8eXXvA6';
    const APISECRET = 'shellmound';
    const CONTENTCAFEID = 'Innovative';
    const CONTENTCAFEPASSWORD = 'Goldengate';

    /*
    When the patron places a hold, what pickup location do you want it to default to in the dropdown list?
    This value needs to be the pickup location code NOT the full spelled out description.
    */
    const DEFAULTHOLDPICKUPLOCATIONCODE = 'm';
    /*
    When a user leaves a comment on the contact page it gets logged to a
    local CSV file. We also have the option of sending an email to a library
    staff member with the comment data. If you choose to have email sent be
    sure and enter a valid email address for the recipient and reply to.
    */
    const COMMENTSENDEMAIL = '1'; // enter 1 to send email. 0 to NOT send email.
    const COMMENTSENDER = 'kelly.wolfe@iii.com'; // the email address identified as the originator
    const COMMENTREPLYTO = 'kelly.wolfe@iii.com';  // the email address where replies should be sent.
    const COMMENTRECIPIENT = 'kelly.wolfe@iii.com';  // the email address to send the comment to.
    /*
    The carousel is populated by a json search query generated from a review file in Sierra. If you want
    to have the carousel present a different set of results, just put your json search query in a text
    file and locate that file in the json subdirectory and indicate the name of your query file here.
     */
    const CAROUSELQUERYFILENAME = 'facpubs.json';

    /*
    even though a data set includes a reference to a specific title, if that title does NOT have an ISBN,
    then we should exclude it from the array of bibs used to generate the carousel data. The intent is to
    avoid including titles that won't be able to display a cover image.
     */
    const EXCLUDEBIBSWITHOUTISBN = '1';

    /*
     * retrieving the search results and full bib data for the carousel takes a lot of time (about 10-15 seconds). So
     * we cache the data by serializing the bib array to file. Every time the carousel is invoked, we check to see how
     * old the cached data is.  If the cache data is older than the value specified here, we go back to the api's for
     * fresh data (and then serialize the new data).
     */
    const CAROUSELDATAREFRESH = 12;  /* in HOURS */

    /*
     * Bib titles are hyperlinked throughout the app. This determines whether the link takes the user to:
     *      1 = the detail record in ENCORE
     *      0 = the detail record in the local app
     */
    const HYPERLINKBIBSTOENCORE = '0';
}
