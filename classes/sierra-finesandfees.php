<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-apiAccessToken.php");

class sierraPatronFines
{
    protected $config;

    private $apiAccessToken = NULL;

    // an array of holds data built from the JSON returned by the API
    private $patronFines = array();
    private $sortState = array("field"=>"none","direction"=>"none");

    public function __construct($patronID)
    {
        $this->config = new config();

        $newToken = new sierraApiAccessToken();
        $this->apiAccessToken = $newToken->getCurrentApiAccessToken();
        $this->populateFinesData($patronID);
        $this->sortFines('bibTitle');
    }

    private function populateFinesData($patronID)
    {
        // this is where we'll call the Sierra API and get patron information
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . (string)$this->config->getApiVer() . '/patrons/' . $patronID . '/fines';
        $uri .= '?id=' . $patronID;
        $uri .= '&fields=item,assessedDate,chargeType,itemCharge,processingFee,billingFee,paidAmount';

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
        $this->patronFines = $result['entries'];

        curl_close($ch);

        // the patron holds api returns a 'record' value which is the api endpoint (the full uri of the API) to
        // either the bib record or item record depending on the type of hold.  So we need to detect which type
        // of record it is and drill down to bib record so we can get the bib title.
        for ($i = 0; $i < count($this->patronFines); ++$i) {

            $myBibId = null;

            // record number that comes from the API is a full uri.  An endpoint.  We need to strip away all but the actual record number.
            $lastSlash = strrpos($this->patronFines[$i]['item'],'/');
            $recordIdNo = substr($this->patronFines[$i]['item'],$lastSlash+1,strlen($this->patronFines[$i]['item']));

                // we have to go from the hold record to the item record to get the bib id.
                $myItemId = $recordIdNo;
                include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-item.php");
                $myItem = new sierraItem($myItemId);
                $myBibId = $myItem->getFirstBibId();

            include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-bib.php");
            $myBib = new sierraBib($myBibId);
            $this->patronFines[$i]['bibId'] = $myBibId;
            $this->patronFines[$i]['bibTitle'] = $myBib->getBibTitle();

            // create derived money fields
            $this->patronFines[$i]['netCharges'] = $this->patronFines[$i]['itemCharge'] + $this->patronFines[$i]['processingFee'] + $this->patronFines[$i]['billingFee'];
            $this->patronFines[$i]['balanceDue'] = $this->patronFines[$i]['netCharges'] - $this->patronFines[$i]['paidAmount'];
        }
    }

    public function getAFine($index) {
        return $this->patronFines[$index];
    }

    public function getFineIdNumber($index) {
        // CKO id number that comes from the API is a full uri.  An endpoint.  We need to strip away all but the actual record number.
        $lastSlash = strrpos($this->patronFines[$index]['id'],'/');
        $recordIdNo = substr($this->patronFines[$index]['id'],$lastSlash+1,strlen($this->patronFines[$index]['id']));
        return $recordIdNo;
    }

    public function getAssessedDateFormatted($index) {
        $originalDate = $this->patronFines[$index]['assessedDate'];
        if ($originalDate == 0) {
            return "";
        } else {
            $newDate=date('d M Y', strtotime($originalDate));
        }
        return $newDate;
    }

    public function getEncoreDeepLink($index) {
        //the url to the encore server is hardcoded here.  someday we may want to make it set by a config value
        $linkString = 'https://' . $this->config->getPacServer() . '/iii/encore/record/C__Rb';
        $linkString .= $this->patronFines[$index]["bibId"];
        return $linkString;
    }

    public function getBibDetailLink($index) {
        $linkString = '/bibdetail.php';
        $linkString .= '?bibid=' . $this->patronFines[$index]["bibId"];
        return $linkString;
    }

    public function getTotalFines() {
        return count($this->patronFines);
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
    function cmpAssessedDateASC($a, $b) { return strnatcasecmp($a['assessedDate'], $b['assessedDate']); }
    function cmpAssessedDateDESC($a, $b) { return strnatcasecmp($b['assessedDate'], $a['assessedDate']); }
    function cmpChargeTypeASC($a, $b) { return strnatcasecmp($a['chargeType']['display'], $b['chargeType']['display']); }
    function cmpChargeTypeDESC($a, $b) { return strnatcasecmp($b['chargeType']['display'], $a['chargeType']['display']); }
    function cmpNetChargesASC($a, $b) { return strnatcasecmp($a['netCharges'], $b['netCharges']); }
    function cmpNetChargesDESC($a, $b) { return strnatcasecmp($b['netCharges'], $a['netCharges']); }
    function cmpPaidAmountASC($a, $b) { return strnatcasecmp($a['paidAmount'], $b['paidAmount']); }
    function cmpPaidAmountDESC($a, $b) { return strnatcasecmp($b['paidAmount'], $a['paidAmount']); }
    function cmpBalanceDueASC($a, $b) { return strnatcasecmp($a['balanceDue'], $b['balanceDue']); }
    function cmpBalanceDueDESC($a, $b) { return strnatcasecmp($b['balanceDue'], $a['balanceDue']); }

    // $fieldname:               be sure and pass in the fieldname as returned by the API (it's the array key)
    // $direction:              ASC or DESC
    public function sortFines($fieldname) {
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
                    usort($this->patronFines, array($this, "cmpTitleASC")) :
                    usort($this->patronFines, array($this, "cmpTitleDESC"));
                break;
            }
            CASE 'assessedDate' : {
                ($direction == "ASC") ?
                    usort($this->patronFines, array($this, "cmpAssessedDateASC")) :
                    usort($this->patronFines, array($this, "cmpAssessedDateDESC"));
                break;
            }
            CASE 'chargeType' : {
                ($direction == "ASC") ?
                    usort($this->patronFines, array($this, "cmpChargeTypeASC")) :
                    usort($this->patronFines, array($this, "cmpChargeTypeDESC"));
                break;
            }
            CASE 'netCharges' : {
                ($direction == "ASC") ?
                    usort($this->patronFines, array($this, "cmpNetChargesASC")) :
                    usort($this->patronFines, array($this, "cmpNetChargesDESC"));
                break;
            }
            CASE 'paidAmount' : {
            ($direction == "ASC") ?
                usort($this->patronFines, array($this, "cmpPaidAmountASC")) :
                usort($this->patronFines, array($this, "cmpPaidAmountDESC"));
            break;
        }
            CASE 'balanceDue' : {
                ($direction == "ASC") ?
                    usort($this->patronFines, array($this, "cmpBalanceDueASC")) :
                    usort($this->patronFines, array($this, "cmpBalanceDueDESC"));
                break;
            }
        }
    }

}

