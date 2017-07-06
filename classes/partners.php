<?php

class partners
{
    protected $partnerData;

    public function __construct() {
        /* read the json file */
        $this->partnerData = json_decode(file_get_contents("json/partners.json"),true);
    }

    public function getPartnerArray() {
        return $this->partnerData;
    }
}