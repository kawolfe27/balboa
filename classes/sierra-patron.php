<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");
include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-apiAccessToken.php");

class sierraPatron
{
    protected $config;

    private $apiAccessToken = NULL;

    // an array of patron data built from the JSON returned by the API
    private static $patron = array();

    // false until the patron barcode and pin has been validated
    private $validPatron = FALSE;

    public function __construct($patronBarcode,$patronPIN)
    {
        $this->config = new config();

        $newToken = new sierraApiAccessToken();
        $this->apiAccessToken = $newToken->getCurrentApiAccessToken();

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

    private function populatePatronData($patronBarcode) {
        // this is where we'll call the Sierra API and get patron information
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . (string)$this->config->getApiVer() . '/patrons/find';
        $uri .= '?barcode=' . $patronBarcode;
        $uri .= '&fields=id,names,birthDate,barcodes,addresses,phones,emails';

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

        $this->patron = json_decode(curl_exec($ch));

        curl_close($ch);
    }

    public function isValidPatron() {
        return ($this->validPatron == TRUE);
    }

    public function getPatronName() {
        if ($this->patron->names[0] == NULL) {
            return 'PATRON RECORD NOT FOUND';
        } else {
            return $this->patron->names[0];
        }
    }

    public function getFirstNameLastName() {
        $myString = $this->patron->names[0];
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
        $myString = $this->patron->names[0];
        $myArray = explode(', ', $myString);
        if (strpos($myArray[1], " ") == 0) {
            $firstName = $myArray[1];
        } else {
            $firstName = substr ($myArray[1] , 0 , strpos($myArray[1], " "));
        }
        return $firstName;
    }

    public function getPatronID() {
        return $this->patron->id;
    }

    public function getPatronBirthDate() {
        if ($this->patron->birthDate == "") {
            return "";
        } else {
            return $this->patron->birthDate;
        }
    }

    public function getPatronBirthDateFormatted() {
        $originalDate = $this->patron->birthDate;
        if ($originalDate == 0) {
            return "";
        } else {
            $newDate=date('F d, Y', strtotime($originalDate));
        }
        return $newDate;
    }

    public function getPatronAddressLines() {
        return $this->patron->addresses[0];
    }

    public function getTotalPhoneNumbers() {
        return sizeof($this->patron->phones);
    }

    public function formattedPhoneNumber($i) {
        $phoneString = $this->patron->phones[$i]->number;
        if(strlen($phoneString) == 10)
        {
            $result = "(" . substr($phoneString,0,3) . ") " . substr($phoneString,3,3) . "-" . substr($phoneString,6);
            return $result;
        } else {
            // we don't know what to do if it's a non-10 digit number so do nothing
            return $phoneString;
        }
    }

    public function getTotalEmails() {
        return sizeof($this->patron->emails);
    }

    public function getPatronEmail($i) {
        return $this->patron->emails[$i];
    }

    public function getTotalBarcodes() {
        return sizeof($this->patron->barcodes);
    }

    public function getPatronBarcode($i) {
        return $this->patron->barcodes[$i];
    }

    /* We don't really want to get the actual patron PIN.  This is just a placeholder. */
    public function getPatronPin() {
        return "***********************";
    }

    public static function setPatronPin($patronId,$newPin) {
        $config = new config();

        $uri = 'https://' . $config->getDB() . ':443/iii/sierra-api/v' . (string)$config->getApiVer() . '/patrons/' . $patronId;

        // Build the header
        $apiAccessToken = new sierraApiAccessToken();
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
}
