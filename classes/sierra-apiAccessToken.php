<?php

// if the constant is already defined, then the CRON script did it and we don't need to set it
defined('APP_ROOT') or include_once('../root-config.php');

include_once(APP_ROOT . "classes/config.php");
class sierraApiAccessToken
{
    protected $config;

    public function __construct() {
        $this->config = new config();
        if (!isset($_SESSION['apiAccessToken'])) {
            $this->setApiAccessToken();
        }
    }

    private function setApiAccessToken()
    {
        $uri = 'https://' . $this->config->getDB() . ':443/iii/sierra-api/v' . $this->config->getApiVer() . '/token/';
        $authCredentials = base64_encode($this->config->getApiKey() . ':' . $this->config->getApiSecret());

        // Build the header
        $headers = array(
            "Authorization: Basic " . $authCredentials,
            "Content-Type: application/x-www-form-urlencoded"
        );

        // make the request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $uri);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');

        $result = json_decode(curl_exec($ch));

        curl_close($ch);

        // save the access token and creation time to a session variable
        $_SESSION['apiAccessToken'] = $result->access_token;
        $_SESSION['apiAccessTokenCreationDate'] = time();
    }

    public function getCurrentApiAccessToken() {

        $now = time();
        $elapsedTime = $now - $_SESSION['apiAccessTokenCreationDate'];

        if ($elapsedTime >= 360) {
            // if the current token is older than 6 minutes, get a new one
            $this->setApiAccessToken();
        }

        return $_SESSION['apiAccessToken'];
    }
}