<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-apiAccessToken.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/locale-strings.php");

class sierraPatron
{
    /** @var config $config */
    protected $config;

    private $apiAccessToken = NULL;

    // an array of patron data built from the JSON returned by the API
    // hopefully this will go away

    private $id;
    private $names = array();
    private $birthDate;
    private $barcodes = array();
    private $addresses = array();
    private $phones = array();
    private $emails = array();
    private $expirationDate;

    // false until the patron barcode and pin has been validated
    private $validPatron = FALSE;

    private $dateFormatString = 'F d, Y'; // the US format

    public function __construct($config,$patronBarcode,$patronPIN)
    {
        $this->config = $config;

        $newToken = new sierraApiAccessToken($this->config);
        $this->apiAccessToken = $newToken->getCurrentApiAccessToken();

        // set the format string based on the locale setting in the config file.
        switch ($this->config->getLocale()) {
            case 'CN' :
            case 'UK' : $this->dateFormatString = 'd F, Y';
            break;
            default:
                $this->dateFormatString = 'F d, Y';
        }


        $this->setValidPatron($patronBarcode,$patronPIN);
        if ($this->validPatron == FALSE) {
            return FALSE;
        } else {
            $this->populatePatronData($patronBarcode);
            return TRUE;
        }
    }

    private function setValidPatron($patronBarcode,$patronPIN) {
        // make the request
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . (string)$this->config->getApiVer() . '/patrons/validate';
        $postFields = json_encode(array('barcode' => $patronBarcode, 'pin' => $patronPIN));
        $header = array(
            "Authorization: Bearer " . $this->apiAccessToken,
            "Content-Type: application/json"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header );

        $serverOutput = json_decode(curl_exec($ch));
        curl_close($ch);

        // currently it seems the API returns NO DATA if the validation was successful. A message if authentication fails.
        // so -- we'll just say we've got a valid patron if zilch comes back from the curl request.
        $this->validPatron = ($serverOutput === NULL);
    }

    public function purgeExpiredCacheData() {
        foreach (new DirectoryIterator('store/patron') as $fileinfo) {

            // IF...
            // the current file is not . or ..
            // the current file is not the cleanup.php or cleanup.log file
            // the file was last created/updated over 24 hours ago
            // THEN...
            //
            // delete the file
            $fileHoursOld = (time() - $fileinfo->getMTime()) / 3600;

            if ($fileinfo->isDot()) {
                continue;
            }

            if ($fileHoursOld < 24) {
                continue;
            }

            unlink('store/patron/' . $fileinfo->getFilename());
        }
    }

    private function populatePatronData($patronBarcode) {
        // this is where we'll call the Sierra API and get patron information
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . (string)$this->config->getApiVer() . '/patrons/find';
        $uri .= '?barcode=' . $patronBarcode;
        $uri .= '&fields=id,names,birthDate,barcodes,addresses,phones,emails,expirationDate';

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

        $result = json_decode(curl_exec($ch));
        curl_close($ch);

        // new data structure
        $this->id = $result->id;
        $this->names = $result->names;
        $this->birthDate = $result->birthDate;
        $this->barcodes = $result->barcodes;
        $this->addresses = $result->addresses;
        $this->phones = $result->phones;
        $this->emails = $result->emails;
        $this->expirationDate = $result->expirationDate;
    }

    public function isValidPatron() {
        return ($this->validPatron == TRUE);
    }

    public function getPatronName() {
        if ($this->names[0] == NULL) {
            return 'PATRON RECORD NOT FOUND';
        } else {
            return $this->names[0];
        }
    }

    public function getNames() {
        return $this->names;
    }

    public function getFirstNameLastName() {
        $myString = $this->names[0];
        $myArray = explode(', ', $myString);
        if (strpos($myArray[1], " ") == 0) {
            $firstName = $myArray[1];
        } else {
            $firstName = substr ($myArray[1] , 0 , strpos($myArray[1], " "));
        }
        $lastName = $myArray[0];
        return $firstName . " " . $lastName;
    }

    public function getFirstName() {
        $myString = $this->names[0];
        $myArray = explode(', ', $myString);
        if (strpos($myArray[1], " ") == 0) {
            $firstName = $myArray[1];
        } else {
            $firstName = substr ($myArray[1] , 0 , strpos($myArray[1], " "));
        }
        return $firstName;
    }

    public function getPatronID() {
        return $this->id;
    }

    public function getPatronBirthDate() {
        if ($this->birthDate == "") {
            return "";
        } else {
            return $this->birthDate;
        }
    }

    public function getPatronBirthDateFormatted() {
        $originalDate = $this->birthDate;
        if ($originalDate == 0) {
            return "";
        } else {
            $newDate=date($this->dateFormatString, strtotime($originalDate));
        }
        return $newDate;
    }

    public function getPatronAddresses() {
        return $this->addresses;
    }

    public function getPhones() {
        return $this->phones;
    }

    public function formattedPhoneNumber($thisPhone) {
        $phoneString = $thisPhone->number;
        if(strlen($phoneString) == 10)
        {
            $result = "(" . substr($phoneString,0,3) . ") " . substr($phoneString,3,3) . "-" . substr($phoneString,6);
            return $result;
        } else {
            // we don't know what to do if it's a non-10 digit number so do nothing
            return $phoneString;
        }
    }

    public function getEmails() {
        return $this->emails;
    }

    public function getFirstEmail() {
        if (count($this->emails = 0)) {
            return "";
        } else {
            return $this->emails[0];
        }
    }

    public function getBarcodes() {
        return $this->barcodes;
    }

    /* We don't really want to get the actual patron PIN.  This is just a placeholder. */
    public function getPatronPin() {
        return "***********************";
    }

    public function getPatronExpirationDate() {
        if ($this->expirationDate == "") {
            return "";
        } else {
            return $this->expirationDate;
        }
    }

    public function getPatronExpirationDateFormatted() {
        $originalDate = $this->expirationDate;
        if ($originalDate == 0) {
            return "";
        } else {
            $newDate=date($this->dateFormatString, strtotime($originalDate));
        }
        return $newDate;
    }

    public static function setPatronPin($patronId,$newPin) {
        $config = new config();

        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/' . $patronId;

        // Build the header
        $apiAccessToken = new sierraApiAccessToken($config);
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken,
            "Content-Type: application/json"
        );

        $data = json_encode(array('pin'=>(string)$newPin));

        // make the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);


        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // if it was successful, update the session

        if (is_null($result)) {
            $_SESSION['pin'] = $newPin;
        }

        // returns NULL if successful; error data if not
        return $result;
    }

    public static function updatePatronEmail($patronId,$newEmailArray)
    {
        $config = new config();

        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/' . $patronId;

        // Build the header
        $apiAccessToken = new sierraApiAccessToken($config);
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken,
            "Content-Type: application/json"
        );

        if (count($newEmailArray) == 0) {
            $data = '{"emails":[""]}';  // representing the JSON for an empty array. (where we're deleting the last email in the bunch)
        } else {
            $data = json_encode(array('emails' => $newEmailArray));
        }

        // make the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // returns NULL if successful; error data if not
        return $result;
    }

    public static function updatePatronPhone($patronId,$newPhoneArray)
    {
        $config = new config();

        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/' . $patronId;

        // Build the header
        $apiAccessToken = new sierraApiAccessToken($config);
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken,
            "Content-Type: application/json"
        );

        if (count($newPhoneArray) == 0) {
            $data = '{"phones":[""]}';  // representing the JSON for an empty array. (where we're deleting the last phone in the bunch)
        } else {
            $data = json_encode(array('phones' => $newPhoneArray));
        }

        // make the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // returns NULL if successful; error data if not
        return $result;
    }

    public static function updatePatronAddress($patronId,$newAddressArray)
    {
        $config = new config();

        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/' . $patronId;

        // Build the header
        $apiAccessToken = new sierraApiAccessToken($config);
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $headers = array(
            "Authorization: Bearer " . $newToken,
            "Content-Type: application/json"
        );

        if (count($newAddressArray) == 0) {
            $data = '{"addresses":[""]}';  // representing the JSON for an empty array. (where we're deleting the last address in the bunch)
        } else {
            $data = json_encode(array('addresses' => $newAddressArray));
        }

        // make the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // returns NULL if successful; error data if not
        return $result;
    }

    public static function addPatron($newPatronArray) {

        $config = new config();


        // make the request
        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/';

        $apiAccessToken = new sierraApiAccessToken($config);
        $newToken = $apiAccessToken->getCurrentApiAccessToken();
        $postFields = json_encode($newPatronArray);

        $header = array(
            "Authorization: Bearer " . $newToken,
            "Content-Type: application/json"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$header );

        $serverOutput = json_decode(curl_exec($ch),true);
        curl_close($ch);

        // if successful, the API returns the new patron's id number
        return $serverOutput;

    }
}
