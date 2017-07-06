<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-apiAccessToken.php");

class sierraItem
{
    protected $config;

    private $apiAccessToken = NULL;
    private $itemData = array();

    public function __construct($itemId) {
        $this->config = new config();

        $newToken = new sierraApiAccessToken();
        $this->apiAccessToken = $newToken->getCurrentApiAccessToken();

        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . (string)$this->config->getApiVer() . '/items/' . $itemId;
        $uri .= '?id=' . $itemId;
        $uri .= '&fields=bibIds,itemType,location,status,callNumber';

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
        curl_close($ch);

        $this->itemData = $result;
    }

    public function getFirstBibId() {
        return $this->itemData['bibIds'][0];
    }

    public function getItemType() {
        return $this->itemData['itemType'];
    }

    public function getCallNumber() {
        return $this->itemData['callNumber'];
    }

    public function getLocation() {
        return $this->itemData['location']['name'];
    }

    public function getStatusCode() {
        return trim($this->itemData['status']['code']);
    }

    public function getStatusDisplay() {
        return $this->itemData['status']['display'];
    }

    public function getDueDate() {
        return $this->itemData['status']['duedate'];
    }
}