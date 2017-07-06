<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-apiAccessToken.php");

abstract class sierraPatronCheckouts
{
    protected $config;

    protected $patronID;
    protected $apiAccessToken = NULL;

    // an array of checkout data built from the JSON returned by the API
    protected $checkouts = array();

    public function __construct($patronID)
    {
        $this->config = new config();

        $this->patronID = $patronID;
        $newToken = new sierraApiAccessToken();
        $this->apiAccessToken = $newToken->getCurrentApiAccessToken();
        $this->populateCheckoutData($patronID);
   }

    protected function populateCheckoutData($patronID)
    {
        // this is where we'll call the Sierra API and get patron information
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . (string)$this->config->getApiVer() . '/patrons/' . $patronID . '/checkouts';
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
    }

    public function getACheckout($index) {
        return $this->checkouts[$index];
    }

    public function getCheckoutItemIdArray() {
        $idArray = array();
        for ($i = 0; $i < count($this->checkouts); $i++) {
            $idArray[] = $this->getCheckoutItemIdNumber($i);
        }
        return $idArray;
    }

    public function isOverdue($index) {
        if (time() >= strtotime('+1 day', strtotime($this->checkouts[$index]["dueDate"]))) {
            return true;
        } else {
            return false;
        }
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

    public function getCheckoutsArray() {
        return $this->checkouts;
    }

    public function getTotalCheckouts() {
        return count($this->checkouts);
    }

    public function getCheckoutIdNumber($index) {
        // CKO id number that comes from the API is a full uri.  An endpoint.  We need to strip away all but the actual record number.
        $lastSlash = strrpos($this->checkouts[$index]['id'],'/');
        $recordIdNo = substr($this->checkouts[$index]['id'],$lastSlash+1,strlen($this->checkouts[$index]['id']));
        return $recordIdNo;
    }

    public function getCheckoutItemIdNumber($index) {
        // CKO id number that comes from the API is a full uri.  An endpoint.  We need to strip away all but the actual record number.
        $lastSlash = strrpos($this->checkouts[$index]['item'],'/');
        $recordIdNo = substr($this->checkouts[$index]['item'],$lastSlash+1,strlen($this->checkouts[$index]['id']));
        return $recordIdNo;
    }

    public static function renewACheckout($ckoId) {
        // call the renewal api and renew the item whose id number is $ckoId

        $config = new config();

        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/checkouts/' . $ckoId . '/renewal';

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
