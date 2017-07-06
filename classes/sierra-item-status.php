<?php

class sierraItemStatus
{
    private $itemStatusArray = array();

    public function __construct()
    {
        $jsonArray = file_get_contents('json/item-status-list.json');
        $this->itemStatusArray = json_decode($jsonArray, TRUE);
    }

    public function getItemStatusDescription($itemStatusCode) {
        return $this->itemStatusArray['item status'][$itemStatusCode];
    }
}
