<?php

class config
{
    // these class variables all have defaults but if there's a serialized file of saved values, the constructor
    // will override these values with the user-specified ones.  These defaults should ONLY be in effect when the
    // app is first used.

    // software version number. should be updated here every time we "release" a set of new functionality.
    private $versionNo = '1.2.73';

    /* GENERAL */
    private $institutionName = 'Balboa University Library';
    private $logoFileName = 'images/library-logo.png';
    private $bootstrapTheme = '/css/balboa.css';
    private $locale = 'US';      /* options are US, CN and UK */
    private $supportToDoList = true;

    /* SERVERS */
    private $db = 'sierra-academic.iii.com';
    private $pacServer = 'encore-academic.iii.com';
    private $irServer = 'encorecalstate.iii.com';
    private $vitalServer = 'vital-share.iii.com:8080';

    /* SIERRA API */
    private $apiVer = '4';
    private $apiKey = 'zIK+9ysnxp8WmcQe1eWxf8eXXvA6';
    private $apiSecret = 'shellmound';

    /* ENRICHED CONTENT CREDENTIALS */
    private $contentCafeID = "Innovative";
    private $contentCafePassword = "Goldengate";

    /*
    When a user leaves a comment on the contact page it gets logged to a
    local CSV file. We also have the option of sending an email to a library
    staff member with the comment data. If you choose to have email sent be
    sure and enter a valid email address for the recipient and reply to.
    */

    /* USER COMMENT EMAIL */
    private $commentSendEmail = '1';      // enter 1 to send email. 0 to NOT send email.
    private $commentSender = 'kelly.wolfe@iii.com';
    private $replyTo = 'NO-REPLY@iii.com';
    private $commentRecipient = 'kelly.wolfe@iii.com';


    /* CAROUSEL */

    /*
    The text which displays as the header to the book carousel
    */
    private $carouselTitle = 'Faculty Publications';
    /*
    The carousel is populated by a json search query generated from a review file in Sierra. The default here is a
    query that Kelly put together that works well (as of this writing).
    */
    private $carouselQueryString ='{"queries":[{"target":{"record":{"type":"bib"},"field":{"tag":"n"}},"expr":{"op":"equals","operands":["sierra academic",""]}},"and",{"target":{"record":{"type":"bib"},"id":83},"expr":{"op":"equals","operands":["03-06-2017",""]}}]}';
    /*
    * retrieving the search results and full bib data for the carousel takes a lot of time (about 10-15 seconds). So
    * we cache the data by serializing the bib array to file. Every time the carousel is invoked, we check to see how
    * old the cached data is.  If the cache data is older than the value specified here, we go back to the api's for
    * fresh data (and then serialize the new data).
    */
    private $carouselDataRefresh = '0';


    /* SEARCH RESULTS */

    /*
    even though a data set includes a reference to a specific title, if that title does NOT have an ISBN,
    then we should exclude it from the array of bibs used to generate the carousel data. The intent is to
    avoid including titles that won't be able to display a cover image.
    */
    private $excludeBibsWithoutISBN = '1';

    /*
    * Bib titles are hyperlinked throughout the app. This determines whether the link takes the user to:
    *      1 = the detail record in ENCORE
    *      0 = the detail record in the local app
    */
    private $hyperlinkBibsToEncore = '0';


    /* HOLDS */

    /*
    When the patron places a hold, what pickup location do you want it to default to in the dropdown list?
    This value needs to be the pickup location code NOT the full spelled out description.
    */
    private $defaultHoldPickupLocationCode = 'm';


    public function __construct()
    {
        // the class will use defaults unless we find a serialized file with saved values
        $configFilename = $_SERVER['DOCUMENT_ROOT'] . "/store/config/config.dat";
        if (file_exists($configFilename)) {
            $serialData = file_get_contents($configFilename);
            $configObj = unserialize($serialData);
            if (!is_object($configObj)) {
                // the existing saved config file couldn't be instantiated as an object. Probably because we've added
                // new properties to the class. So we'll create a new config file from our current defaults.
                $this->serializeConfigData();
            }
            $vars = get_object_vars($configObj);
            foreach ($vars as $name => $value) {
                $this->$name = $value;
            }
        } else {
            // we really shouldn't need to do this.  The user will update the config and the saved data should be serialized then.
            $this->serializeConfigData();
        }
    }

    public function serializeConfigData() {
        $serialData = serialize($this);
        file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/store/config/config.dat", $serialData);
    }


    // we remove the config file so the app will have to revert to the defaults.
    public static function removeConfigFile() {
        unlink($_SERVER['DOCUMENT_ROOT'] . "/store/config/config.dat");
    }

    /* PROPERTY GET AND SET METHODS */

    public function setInstitutionName($institutionName) {
        $this->institutionName = $institutionName;
    }

    public function getInstitutionName() {
        return $this->institutionName;
    }

    public function setBootstrapTheme($bootstrapTheme) {
        $this->bootstrapTheme = $bootstrapTheme;
    }

    public function getBootstrapTheme() {
        return $this->bootstrapTheme;
    }

    public function setLocale($locale) {
        $this->locale = $locale;
    }

    public function getLocale() {
        return $this->locale;
    }

    public function setSupportToDoList($supportToDoList) {
        $this->supportToDoList = $supportToDoList;
    }

    public function getSupportToDoList() {
        return $this->supportToDoList;
    }

    public function setDB($db) {
        $this->db = $db;
    }

    public function getDB() {
        return $this->db;
    }

    public function setPacServer($pacServer) {
        $this->pacServer = $pacServer;
    }

    public function getPacServer() {
        return $this->pacServer;
    }

    public function setIrServer($irServer) {
        $this->irServer = $irServer;
    }

    public function getIrServer() {
        return $this->irServer;
    }

    public function setVitalServer($vitalServer) {
        $this->vitalServer = $vitalServer;
    }

    public function getVitalServer() {
        return $this->vitalServer;
    }

    public function setApiVer($apiVer) {
        $this->apiVer = $apiVer;
    }

    public function getApiVer() {
        return $this->apiVer;
    }

    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    public function setApiSecret($apiSecret) {
        $this->apiSecret = $apiSecret;
    }

    public function getApiSecret() {
        return $this->apiSecret;
    }

    public function setContentCafeID($ContentCafeID) {
        $this->contentCafeID = $ContentCafeID;
    }

    public function getContentCafeID() {
        return $this->contentCafeID;
    }

    public function setContentCafePassword($contentCafePassword) {
        $this->contentCafePassword = $contentCafePassword;
    }

    public function getContentCafePassword() {
        return $this->contentCafePassword;
    }

    public function setDefaultHoldPickupLocationCode($defaultHoldPickupLocationCode) {
        $this->defaultHoldPickupLocationCode = $defaultHoldPickupLocationCode;
    }

    public function getDefaultHoldPickupLocationCode() {
        return $this->defaultHoldPickupLocationCode;
    }

    public function setCommentSendEmail($commentSendEmail) {
        $this->commentSendEmail = $commentSendEmail;
    }

    public function getCommentSendEmail() {
        return $this->commentSendEmail;
    }

    public function setCommentSender($commentSender) {
        $this->commentSender = $commentSender;
    }

    public function getCommentSender() {
        return $this->commentSender;
    }

    public function setSendTo($replyTo) {
        $this->replyTo = $replyTo;
    }

    public function getReplyTo() {
        return $this->replyTo;
    }

    public function setCommentRecipient($commentRecipient) {
        $this->commentRecipient = $commentRecipient;
    }

    public function getCommentRecipient() {
        return $this->commentRecipient;
    }

    public function setCarouselTitle($carouselTitle) {
        $this->carouselTitle = $carouselTitle;
    }

    public function getCarouselTitle() {
        return $this->carouselTitle;
    }

    public function setCarouselQueryFilename($carouselQueryString) {
        $this->carouselQueryString = $carouselQueryString;
    }

    public function getCarouselQueryString() {
        return $this->carouselQueryString;
    }

    public function setExcludeBibsWithoutISBN($excludeBibsWithoutISBN) {
        $this->excludeBibsWithoutISBN = $excludeBibsWithoutISBN;
    }

    public function getExcludeBibsWithoutISBN() {
        return $this->excludeBibsWithoutISBN;
    }

    public function setCarouselDataRefresh($carouselDataRefresh) {
        $this->carouselDataRefresh = $carouselDataRefresh;
    }

    public function getCarouselDataRefresh() {
        return $this->carouselDataRefresh;
    }

    public function setHyperlinkBibsToEncore($hyperlinkBibsToEncore) {
        $this->hyperlinkBibsToEncore = $hyperlinkBibsToEncore;
    }

    public function getHyperlinkBibsToEncore() {
        return $this->hyperlinkBibsToEncore;
    }

    public function setLogoFileName($logoFileName) {
        $this->logoFileName = $logoFileName;
    }

    public function getLogoFileName() {
        return $this->logoFileName;
    }

    public function getVersionNo() {
        return $this->versionNo;
    }
