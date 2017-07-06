<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-apiAccessToken.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-item.php");

class sierraPatronHolds
{
    protected $config;

    private $apiAccessToken = NULL;
    private $sortState = array("field"=>"none","direction"=>"none");

    // an array of holds data built from the JSON returned by the API
    private $patronHolds = array();

    public function __construct($patronID)
    {
        $this->config = new config();

        $newToken = new sierraApiAccessToken();
        $this->apiAccessToken = $newToken->getCurrentApiAccessToken();
        $this->populateHoldsData($patronID);
        $this->sortHolds('bibTitle');
    }

    private function populateHoldsData($patronID)
    {
        // this is where we'll call the Sierra API and get patron information
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . (string)$this->config->getApiVer() . '/patrons/' . $patronID . '/holds';
        $uri .= '?id=' . $patronID;
        $uri .= '&fields=id,record,frozen,notNeededAfterDate,pickupLocation,status,recordType,priority,priorityQueueLength';

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
        $this->patronHolds = $result['entries'];
        curl_close($ch);

        // the patron holds api returns a 'record' value which is the api endpoint (the full uri of the API) to
        // either the bib record or item record depending on the type of hold.  So we need to detect which type
        // of record it is and drill down to bib record so we can get the bib title.
        for ($i = 0; $i < count($this->patronHolds); ++$i) {

            $myBibId = null;

            // record number that comes from the API is a full uri.  An endpoint.  We need to strip away all but the actual record number.
            $lastSlash = strrpos($this->patronHolds[$i]['record'],'/');
            $recordIdNo = substr($this->patronHolds[$i]['record'],$lastSlash+1,strlen($this->patronHolds[$i]['record']));

            if (strpos($this->patronHolds[$i]['record'],"bib", 0) !== false) {
                // the record ID is a bib number
                $myBibId = $recordIdNo;
            } else {
                // the record ID is an item number

                // we have to go from the hold record to the item record to get the bib id.
                $myItemId = $recordIdNo;
                include_once($_SERVER['DOCUMENT_ROOT'] .  "/classes/sierra-item.php");
                $myItem = new sierraItem($myItemId);
                $myBibId = $myItem->getFirstBibId();
            }

            include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-bib.php");
            $myBib = new sierraBib($myBibId);
            $this->patronHolds[$i]['bibTitle'] = $myBib->getBibTitle();
            $this->patronHolds[$i]['bibId'] = $myBibId;
        }
    }

    public function getEncoreDeepLink($index) {
        //the url to the encore server is hardcoded here.  someday we may want to make it set by a config value
        $linkString = 'https://encore-public.iii.com/iii/encore/record/C__Rb';
        $linkString .= $this->patronHolds[$index]["bibId"];
        return $linkString;
    }

    public function getBibDetailLink($index) {
        $linkString = '/bibdetail.php';
        $linkString .= '?bibid=' . $this->patronHolds[$index]["bibId"];
        return $linkString;
    }

    public function getAHold($index) {
        return $this->patronHolds[$index];
    }

    public function getNotNeededAfterDateFormatted($index) {
        $originalDate = $this->patronHolds[$index]['notNeededAfterDate'];
        if ($originalDate == 0) {
            return "";
        } else {
            $newDate=date('d M Y', strtotime($originalDate));
        }
        return $newDate;
    }

    public function hasCurrentlyOnHold($bibId) {
        for ($i = 0; ($i < count($this->patronHolds)); $i++) {
            $myHoldBibId = $this->getABibIdNumber($i);
            if ($myHoldBibId == $bibId) {
                return true;
            }
        }
        return false;
    }

    public function getTotalHolds() {
        return count($this->patronHolds);
    }

    public function getSortDirection() {
        return $this->sortState['direction'];
    }

    public function getSortField() {
        return $this->sortState['field'];
    }

    public function getAHoldIdNumber($index) {
        // Hold id number that comes from the API is a full uri.  An endpoint.  We need to strip away all but the actual record number.
        $lastSlash = strrpos($this->patronHolds[$index]['id'],'/');
        $recordIdNo = substr($this->patronHolds[$index]['id'],$lastSlash+1,strlen($this->patronHolds[$index]['id']));
        return $recordIdNo;
    }

    public function getABibIdNumber($index) {
        // Bib id number that comes from the API is a full uri.  An endpoint.  We need to strip away all but the actual record number.
        $lastSlash = strrpos($this->patronHolds[$index]['record'],'/');
        $recordIdNo = substr($this->patronHolds[$index]['record'],$lastSlash+1,strlen($this->patronHolds[$index]['id']));

        switch ($this->patronHolds[$index]['recordType']) {
            case "b":
                return $recordIdNo;
                break;
            case "i": /* item level hold */
            case "j": /* volume level hold */
                // item AND volume level holds both use the item id number for their record number. whew.
                $thisItem = new sierraItem($recordIdNo);
                return $thisItem->getFirstBibId();
                break;
        }

        return $recordIdNo;
    }

    public static function placeAHold($patronId,$bibId,$pickupLocation,$neededBy) {
        $config = new config();

        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/' . $patronId . '/holds/requests';

        $data_array = array(
            'recordType'=>"b",
            'recordNumber'=>(int)$bibId,
            'pickupLocation'=>$pickupLocation
        );

        // the needed by date is optional, so only include this array element if the user provided a date.
        if (!$neededBy == "") {
            $data_array['neededBy'] = $neededBy;
        }

        $data_json = json_encode($data_array);

        // Build the header
        $apiAccessToken = new sierraApiAccessToken();
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken,
            "Content-Type: application/json",
            "Content-Length: " . strlen($data_json)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = json_decode(curl_exec($ch),TRUE);
        curl_close($ch);

        return $response;
    }

    public static function changePickupLocation($holdId,$newLocation) {
        $config = new config();

        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . $config->getApiVer() . '/patrons/holds/' . $holdId;

        $data_json = json_encode(array('pickupLocation'=>$newLocation));

        // Build the header
        $apiAccessToken = new sierraApiAccessToken();
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken,
            "Content-Type: application/json",
            "Content-Length: " . strlen($data_json)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public static function cancelAHold($holdId) {
        // delete the hold with id number $holdId

        $config = new config();

        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/holds/' . $holdId;

        // Build the header
        $apiAccessToken = new sierraApiAccessToken();
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken
        );

        $data_json = json_encode(array('holdId'=>$holdId));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response  = curl_exec($ch);
        curl_close($ch);

        // returns NULL if successful; error data if not
        return $response;
    }

    public static function freezeAHold($holdId) {
        $config = new config();
        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/holds/' . $holdId;

        $data_json = json_encode(array('freeze'=>true));

        echo $holdId;

        // Build the header
        $apiAccessToken = new sierraApiAccessToken();
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken,
            "Content-Type: application/json",
            "Content-Length: " . strlen($data_json)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public static function unFreezeAHold($holdId) {
        $config = new config();
        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/holds/' . $holdId;

        $data_json = json_encode(array('freeze'=>false));

        echo $holdId;

        // Build the header
        $apiAccessToken = new sierraApiAccessToken();
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken,
            "Content-Type: application/json",
            "Content-Length: " . strlen($data_json)
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response  = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    /* SORTING ROUTINES */

    function cmpTitleASC($a, $b) { return strnatcasecmp($a['bibTitle'], $b['bibTitle']); }
    function cmpTitleDESC($a, $b) { return strnatcasecmp($b['bibTitle'], $a['bibTitle']); }
    function cmpFrozenASC($a, $b) { return strnatcasecmp($a['frozen'], $b['frozen']); }
    function cmpFrozenDESC($a, $b) { return strnatcasecmp($b['frozen'], $a['frozen']); }
    function cmpNotNeededAfterDateASC($a, $b) { return strnatcasecmp($a['notNeededAfterDate'], $b['notNeededAfterDate']); }
    function cmpNotNeededAfterDateDESC($a, $b) { return strnatcasecmp($b['notNeededAfterDate'], $a['notNeededAfterDate']); }
    function cmpPickupLocationASC($a, $b) { return strnatcasecmp($a['pickupLocation']['name'], $b['pickupLocation']['name']); }
    function cmpPickupLocationDESC($a, $b) { return strnatcasecmp($b['pickupLocation']['name'], $a['pickupLocation']['name']); }
    function cmpPriorityASC($a, $b) { return strnatcasecmp($a['priority'], $b['priority']); }
    function cmpPriorityDESC($a, $b) { return strnatcasecmp($b['priority'], $a['priority']); }


    // $fieldname:               be sure and pass in the fieldname as returned by the API (it's the array key)
    public function sortHolds($fieldname) {
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
                    usort($this->patronHolds, array($this, "cmpTitleASC")) :
                    usort($this->patronHolds, array($this, "cmpTitleDESC"));
                break;
            }
            CASE 'frozen' : {
                ($direction == "ASC") ?
                    usort($this->patronHolds, array($this, "cmpFrozenASC")) :
                    usort($this->patronHolds, array($this, "cmpFrozenDESC"));
                break;
            }
            CASE 'notNeededAfterDate' : {
                ($direction == "ASC") ?
                    usort($this->patronHolds, array($this, "cmpNotNeededAfterDateASC")) :
                    usort($this->patronHolds, array($this, "cmpNotNeededAfterDateDESC"));
                break;
            }
            CASE 'pickupLocation' : {
                ($direction == "ASC") ?
                    usort($this->patronHolds, array($this, "cmpPickupLocationASC")) :
                    usort($this->patronHolds, array($this, "cmpPickupLocationDESC"));
                break;
            }
            CASE 'priority' : {
                ($direction == "ASC") ?
                    usort($this->patronHolds, array($this, "cmpPriorityASC")) :
                    usort($this->patronHolds, array($this, "cmpPriorityDESC"));
                break;
            }
        }
    }
}
