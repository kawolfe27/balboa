<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-apiAccessToken.php");

class sierraPatronHistory
{
    protected $config;

    private $apiAccessToken = NULL;

    // an array of holds data built from the JSON returned by the API
    private $patronHistory = array();
    private $sortState = array("field"=>"none","direction"=>"none");
    private $patronID;

    public function __construct($patronID)
    {
        $this->config = new config();

        $newToken = new sierraApiAccessToken();
        $this->apiAccessToken = $newToken->getCurrentApiAccessToken();
        $this->populateHistoryData($patronID);
        $this->sortHistory('outDate');
        $this->patronID = $patronID;
    }

    private function populateHistoryData($patronID)
    {
        // this is where we'll call the Sierra API and get patron information
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . (string)$this->config->getApiVer() . '/patrons/' . $patronID . '/checkouts/history';
        $uri .= '?id=' . $patronID;
        $uri .= '&fields=item,bib,outDate';


        // Build the header
        $headers = array(
            "Authorization: Bearer " . $this->apiAccessToken
        );

        // make the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = json_decode(curl_exec($ch), true);
        $this->patronHistory = $result['entries'];
        curl_close($ch);

        // the patron holds api returns a 'record' value which is the api endpoint (the full uri of the API) to
        // either the bib record or item record depending on the type of hold.  So we need to detect which type
        // of record it is and drill down to bib record so we can get the bib title.
        for ($i = 0; $i < count($this->patronHistory); ++$i) {

            $myBibId = null;

            // record number that comes from the API is a full uri.  An endpoint.  We need to strip away all but the actual record number.
            $lastSlash = strrpos($this->patronHistory[$i]['item'],'/');
            $recordIdNo = substr($this->patronHistory[$i]['item'],$lastSlash+1,strlen($this->patronHistory[$i]['item']));

                // we have to go from the hold record to the item record to get the bib id.
                $myItemId = $recordIdNo;
                include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-item.php");
                $myItem = new sierraItem($myItemId);
                $myBibId = $myItem->getFirstBibId();

            include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-bib.php");
            $myBib = new sierraBib($myBibId);
            $this->patronHistory[$i]['bibId'] = $myBibId;
            $this->patronHistory[$i]['bibTitle'] = $myBib->getBibTitle();
            $this->patronHistory[$i]['bibAuthor'] = $myBib->getBibAuthor();
            $this->patronHistory[$i]['itemType'] = $myItem->getItemType();
            $this->patronHistory[$i]['firstISBN'] = $myBib->getFirstISBN();   // this is just for testing.  delete when done.
        }
    }

    public function getAHistory($index) {
        return $this->patronHistory[$index];
    }

    public function getEncoreDeepLink($index) {
        //the url to the encore server is hardcoded here.  someday we may want to make it set by a config value
        $linkString = 'https://' . $this->config->getPacServer() .'/iii.com/iii/encore/record/';
        $linkString .= 'C__Rb';
        $linkString .= $this->patronHistory[$index]["bibId"];
        return $linkString;
    }

    public function getBibDetailLink($index) {
        $linkString = '/bibdetail.php';
        $linkString .= '?bibid=' . $this->patronHistory[$index]["bibId"];
        return $linkString;
    }

    public function getEncoreDeepLinkAuthor($index) {
        //the url to the encore server is hardcoded here.  someday we may want to make it set by a config value
        $linkString = 'https://' . $this->config->getPacServer() . '/iii/encore/plus/';
        $linkString .= 'C__SA:(' . $this->patronHistory[$index]["bibAuthor"] . ')';
        return $linkString;
    }

    public function getCheckoutDateFormatted($index) {
        $originalDate = $this->patronHistory[$index]['outDate'];
        if ($originalDate == 0) {
            return "";
        } else {
            $newDate=date('d M Y', strtotime($originalDate));
        }
        return $newDate;
    }

    public function getTotalHistory() {
        return count($this->patronHistory);
    }

    public function getSortDirection() {
        return $this->sortState['direction'];
    }

    public function getSortField() {
        return $this->sortState['field'];
    }

    /* SORTING ROUTINES */

    function cmpTitleASC($a, $b) { return strnatcasecmp($a['bibTitle'], $b['bibTitle']); }
    function cmpTitleDESC($a, $b) { return strnatcasecmp($b['bibTitle'], $a['bibTitle']); }
    function cmpAuthorASC($a, $b) { return strnatcasecmp($a['bibAuthor'], $b['bibAuthor']); }
    function cmpAuthorDESC($a, $b) { return strnatcasecmp($b['bibAuthor'], $a['bibAuthor']); }
    function cmpFormatASC($a, $b) { return strnatcasecmp($a['itemType'], $b['itemType']); }
    function cmpFormatDESC($a, $b) { return strnatcasecmp($b['itemType'], $a['itemType']); }
    function cmpOutDateASC($a, $b) { return strnatcasecmp($a['outDate'], $b['outDate']); }
    function cmpOutDateDESC($a, $b) { return strnatcasecmp($b['outDate'], $a['outDate']); }


    // $fieldname:               be sure and pass in the fieldname as returned by the API (it's the array key)
    public function sortHistory($fieldname) {

        // determine the sort direction
        switch (true) {
            case ($this->sortState['field'] == $fieldname):
                // if the fieldname is the same as before, simply toggle the sort direction
                ($this->sortState['direction'] == "ASC") ?
                    $direction = "DESC" :
                    $direction = "ASC";
                break;
            case ($fieldname == "outDate"):
                $direction="DESC";
                break;
            default:
                $direction="ASC";
        }


        // set the new sort status
        $this->sortState["direction"] = $direction;
        $this->sortState['field'] = $fieldname;

        switch ($fieldname) {
            CASE 'bibTitle' : {
                ($direction == "ASC") ?
                    usort($this->patronHistory, array($this, "cmpTitleASC")) :
                    usort($this->patronHistory, array($this, "cmpTitleDESC"));
                break;
            }
            CASE 'bibAuthor' : {
                ($direction == "ASC") ?
                    usort($this->patronHistory, array($this, "cmpAuthorASC")) :
                    usort($this->patronHistory, array($this, "cmpAuthorDESC"));
                break;
            }
            CASE 'itemType' : {
                ($direction == "ASC") ?
                    usort($this->patronHistory, array($this, "cmpFormatASC")) :
                    usort($this->patronHistory, array($this, "cmpFormatDESC"));
                break;
            }
            CASE 'outDate' : {
                ($direction == "ASC") ?
                    usort($this->patronHistory, array($this, "cmpOutDateASC")) :
                    usort($this->patronHistory, array($this, "cmpOutDateDESC"));
                break;
            }
        }
    }

    public function downloadReadingList()
    {
        // write reading list to a file (as currently sorted)
        $readingListFilename = 'reading-list-' . $this->patronID . '.csv';

        // write out a header row
        $headerStr = 'Format,Title,Author,Checkout Date';
        file_put_contents($readingListFilename, $headerStr . PHP_EOL);

        // write out a row for each reading list entry
        foreach ($this->patronHistory as $key => $thisHistoryItem) {
            $thisRowStr =
                $thisHistoryItem['itemType'] . ',' .
                '"' . $thisHistoryItem['bibTitle'] . '",' .
                '"' . $thisHistoryItem['bibAuthor'] . '",' .
                $this->getCheckoutDateFormatted($key);

            file_put_contents($readingListFilename, $thisRowStr . PHP_EOL, FILE_APPEND);
        }

        // download the file
        $file = $readingListFilename;

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
        }

        // delete the local file
        unlink($readingListFilename);
    }
}

