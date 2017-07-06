<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-apiAccessToken.php");

class sierraPatronCheckouts
{
    private $patronID;
    private $apiAccessToken = NULL;
    private $sortState = array("field"=>"none","direction"=>"none");

    // an array of checkout data built from the JSON returned by the API
    private $checkouts = array();

    // protected to ensure it can only be created by itself.
    public function __construct($patronID)
    {
        $this->patronID = $patronID;
        $newToken = new sierraApiAccessToken();
        $this->apiAccessToken = $newToken->getCurrentApiAccessToken();
        $this->populateCheckoutData($patronID);
        $this->sortCheckouts('bibTitle');  // default sort by title.
   }

    private function populateCheckoutData($patronID)
    {
        // this is where we'll call the Sierra API and get patron information
        $uri = 'https://' . config::DB . ':443/iii/sierra-api/v' . (string)config::APIVER . '/patrons/' . $patronID . '/checkouts';
        $uri .= '?id=' . $patronID;
        $uri .= '&fields=id,item,barcode,dueDate,callNumber,numberOfRenewals';

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
        $this->checkouts = $result['entries'];
        curl_close($ch);

        // loop through the checkouts array and add the bib title to each element.
        // we have to get the item id from the checkout record and then bib id from the item record.
        include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-bib.php");
        include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-item.php");

        for ($i = 0; $i < count($this->checkouts); ++$i) {
            $lastSlash = strrpos($this->checkouts[$i]['item'],'/');
            $myItemId = substr($this->checkouts[$i]['item'],$lastSlash+1,strlen($this->checkouts[$i]['item']));
            $myItem = new sierraItem($myItemId);
            $myBib = new sierraBib($myItem->getFirstBibId());
            $this->checkouts[$i]['bibId'] = $myItem->getFirstBibId();
            $this->checkouts[$i]['bibTitle'] = $myBib->getBibTitle();
            $this->checkouts[$i]['itemType'] = $myItem->getItemType();
        }
    }

    public function getACheckout($index) {
        return $this->checkouts[$index];
    }

    public function getItemType($index) {
        return $this->checkouts[$index]['itemType'];
    }

    public function getDueDateFormatted($index) {
        $originalDate = $this->checkouts[$index]['dueDate'];
        if ($originalDate == 0) {
            return "";
        } else {
            $newDate=date('d M Y', strtotime($originalDate));
        }
        return $newDate;
    }

    public function getEncoreDeepLink($index) {
        //the url to the encore server is hardcoded here.  someday we may want to make it set by a config value
        $linkString = 'https://encore-public.iii.com/iii/encore/record/C__Rb';
        $linkString .= $this->checkouts[$index]["bibId"];
        return $linkString;
    }

    public function getBibDetailLink($index) {
        $linkString = '/bibdetail.php';
        $linkString .= '?bibid=' . $this->checkouts[$index]["bibId"];
        return $linkString;
    }
    
    public function getTotalCheckouts() {
        return count($this->checkouts);
    }

    public function getSortDirection() {
        return $this->sortState['direction'];
    }

    public function getSortField() {
        return $this->sortState['field'];
    }

    public function getCheckoutIdNumber($index) {
        // CKO id number that comes from the API is a full uri.  An endpoint.  We need to strip away all but the actual record number.
        $lastSlash = strrpos($this->checkouts[$index]['id'],'/');
        $recordIdNo = substr($this->checkouts[$index]['id'],$lastSlash+1,strlen($this->checkouts[$index]['id']));
        return $recordIdNo;
    }

    /* SORTING ROUTINES */

    function cmpTitleASC($a, $b) { return strnatcasecmp($a['bibTitle'], $b['bibTitle']); }
    function cmpTitleDESC($a, $b) { return strnatcasecmp($b['bibTitle'], $a['bibTitle']); }
    function cmpDueDateASC($a, $b) { return strnatcasecmp($a['dueDate'], $b['dueDate']); }
    function cmpDueDateDESC($a, $b) { return strnatcasecmp($b['dueDate'], $a['dueDate']); }
    function cmpItemTypeASC($a, $b) { return strnatcasecmp($a['itemType'], $b['itemType']); }
    function cmpItemTypeDESC($a, $b) { return strnatcasecmp($b['itemType'], $a['itemType']); }
    function cmpCallNumberASC($a, $b) { return strnatcasecmp($a['callNumber'], $b['callNumber']); }
    function cmpCallNumberDESC($a, $b) { return strnatcasecmp($b['callNumber'], $a['callNumber']); }
    function cmpNumberOfRenewalsASC($a, $b) { return strnatcasecmp($a['numberOfRenewals'], $b['numberOfRenewals']); }
    function cmpNumberOfRenewalsDESC($a, $b) { return strnatcasecmp($b['numberOfRenewals'], $a['numberOfRenewals']); }

    // $fieldname:               be sure and pass in the fieldname as returned by the API (it's the array key)
    // $direction:              ASC or DESC
    public function sortCheckouts($fieldname) {
        // default sort direction is ASC.  UNLESS the field is the same as before.  Then we toggle the direction
        $direction="ASC";
        if ($this->sortState['field'] == $fieldname) {
            if ($this->sortState['direction'] == "ASC") {
                $direction = "DESC";
            } else {
                $direction = "ASC";
            }
        }

        // set the new sort status
        $this->sortState["direction"] = $direction;
        $this->sortState['field'] = $fieldname;

        switch ($fieldname) {
            CASE 'bibTitle' : {
                ($direction == "ASC") ?
                    usort($this->checkouts, array($this, "cmpTitleASC")) :
                    usort($this->checkouts, array($this, "cmpTitleDESC"));
                break;
            }
            CASE 'dueDate' : {
                ($direction == "ASC") ?
                    usort($this->checkouts, array($this, "cmpDueDateASC")) :
                    usort($this->checkouts, array($this, "cmpDueDateDESC"));
                break;
            }
            CASE 'itemType' : {
                ($direction == "ASC") ?
                    usort($this->checkouts, array($this, "cmpItemTypeASC")) :
                    usort($this->checkouts, array($this, "cmpItemTypeDESC"));
                break;
            }
            CASE 'callNumber' : {
                ($direction == "ASC") ?
                    usort($this->checkouts, array($this, "cmpCallNumberASC")) :
                    usort($this->checkouts, array($this, "cmpCallNumberDESC"));
                break;
            }
            CASE 'numberOfRenewals' : {
                ($direction == "ASC") ?
                    usort($this->checkouts, array($this, "cmpNumberOfRenewalsASC")) :
                    usort($this->checkouts, array($this, "cmpNumberOfRenewalsDESC"));
                break;
            }
        }
    }

    public static function renewACheckout($ckoId) {
        // call the renewal api and renew the item whose id number is $ckoId

        $uri = 'https://' . config::DB . ':443/iii/sierra-api/v' . (string)config::APIVER . '/patrons/checkouts/' . $ckoId . '/renewal';

        // Build the header
        $apiAccessToken = new sierraApiAccessToken();
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken
        );

        $data = array('checkoutId'=>$ckoId);

        // make the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $result;
    }
}
