<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-apiAccessToken.php");

class sierraPickupLocations
{
    protected $config;

    private $apiAccessToken = NULL;

    // an array of pickupLocations data built from the JSON returned by the API
    private $pickupLocations = array();

    public function __construct()
    {
        $this->config = new config();

        $newToken = new sierraApiAccessToken();
        $this->apiAccessToken = $newToken->getCurrentApiAccessToken();

        $this->populateLocationData();
        $this->sortLocationData();
    }

    private function populateLocationData()
    {
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . (string)$this->config->getApiVer() . '/internal/metadata/pickupLocations';

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

        // populate the pickupLocations array so that the location description is the key (since we want it to be unique)
        // this varies the array structure somewhat from how the api delivers it.  Making the description field the key
        // to the array makes it easier to do the parsing described below.

        // we kind of have to bend over backwards here. The API returns ALL location codes mapped as pickup locations.
        // so a given main location description like "East Library" may have many multiple sub-locations mapped to it.
        // we want to use just ONE location code per pickup library description.  And if there are multiple duplicatess, we want
        // to use the single character code.

        foreach ($result as $thisResultLocation) {
            // test to see if this key (the location description) is unique.  If the key already exists we want to
            // keep the new code if it is a one character code (thus not a sub-location)
            $existingLocationName = $this->pickupLocations[$thisResultLocation["name"]];
            if ($existingLocationName == NULL)
            {
                // this is a new location name so just add it to the array
                $this->pickupLocations[$thisResultLocation["name"]] = $thisResultLocation["code"];

            } else {
                // this is a duplicate location name. if the code of the new record is a single digit code
                // replace the existing value with the incoming code.
                if (strlen($thisResultLocation["code"]) == 1) {
                    $this->pickupLocations[$thisResultLocation["name"]] = $thisResultLocation['code'];
                }
            }
        }
    }

    private function sortLocationData() {
        // the pickup location name/description is the array key, so sorting is easy-peasy.
        ksort($this->pickupLocations);
    }

    public function getPickupLocationsArray() {
        return $this->pickupLocations;
    }
}