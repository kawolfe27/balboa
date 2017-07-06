<?php

include_once(APP_ROOT . "classes/sierra-bib.php");
include_once(APP_ROOT . "classes/sierra-apiAccessToken.php");
class carouselBibs
{
    private $bibIdArray = array();
    public $carouselBibArray = array();
    protected $config;

    public function __construct($config, $jsonQueryString)
    {
        $this->config = $config;

        // determine if we need to refresh the carousel data cache
        if (!file_exists(APP_ROOT . "store/bib/bib.dat")) {
            $dataRefreshRequired = true;
        } else {
            $diff = time() - filemtime(APP_ROOT . "store/bib/bib.dat");
            $hoursSinceLastRefresh = round($diff / 3600);
            $dataRefreshRequired = ($hoursSinceLastRefresh > $this->config->getCarouselDataRefresh());
        }

        // get the data - either from the cache (fast) or the API calls (slow).
        if ($dataRefreshRequired) {
            // make the API calls to get fresh data
            $this->setBibIdList($jsonQueryString);
            $this->setCarouselBibArray($this->bibIdArray);
            // serialize the fresh data to the cache file
            file_put_contents(APP_ROOT . "store/bib/bib.dat", serialize($this->carouselBibArray));
        } else {
            $cachedData = file_get_contents(APP_ROOT . "store/bib/bib.dat");
            $this->carouselBibArray = unserialize($cachedData);
        }
    }

    // takes a jsonQuery, gives it to the API and returns an array of bib id's resulting from the query
    private function setBibIdList($jsonQueryString) {

        // call the Sierra API and get a list of the bib ids resulting from the query string
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . $this->config->getApiVer() . '/bibs/query';
        $uri .= '?limit=1000';
        $uri .= '&offset=0';

        $apiToken = new sierraApiAccessToken();

        // Build the header
        $headers = array(
            "Authorization: Bearer " . $apiToken->getCurrentApiAccessToken(),
            "Content-Type:  application/json"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonQueryString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // this will give us a JSON string of bib id's (the full api URI)
        $result = curl_exec($ch);
        $bibIdArray = json_decode($result, true); // true means the result will be an array, not an object

        $bibIdArray = $bibIdArray["entries"];

        // iterate through the resulting array
        // strip the bib ids down to just the id number (remove all the uri junk)
        // add the stripped id number as an array element to the class property
        foreach ($bibIdArray as $thisId) {
            $oneId = $this->stripped($thisId["link"]);
            $this->bibIdArray[] = $oneId;
        }
    }

    // returns the bib id stripped of all the uri falderal
    private function stripped($fatId) {
        $lastSlash = strrpos($fatId, '/');
        $strippedId = substr($fatId, $lastSlash + 1, strlen($fatId));
        return $strippedId;
    }

    // iterate through the array of BIB id's and populate the values for each element of the carouselBibArray
    private function setCarouselBibArray($bibIdArray) {
        foreach ($bibIdArray as $thisBibId) {
            $thisBibRecord = new sierraBib($thisBibId);
            // build an array for this bib
            $oneCarouselBib = array(
                'bibId'=>$thisBibId,
                'bibTitle'=>$thisBibRecord->getBibTitle(),
                'bibAuthor'=>$thisBibRecord->getBibAuthor(),
                'pacUrl'=>$thisBibRecord->getPACURL(),
                'imageUrl'=>$thisBibRecord->getBookjacketImageURL()
            );

            // add the array for this bib onto the array of carousel bibs
            $this->carouselBibArray[] = $oneCarouselBib;
        }
    }
}