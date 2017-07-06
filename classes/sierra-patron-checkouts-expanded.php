<!--

This class has extended data (which takes longer to retrieve).  In addition to patron checkout and item ID's this
class retrieves the related full bib and item details.  And also has the ability to SORT the checkouts array by
a variety of elements.

-->


<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-patron-checkouts.php");


class sierraPatronCheckoutsExpanded extends sierraPatronCheckouts
{
    private $sortState = array("field"=>"none","direction"=>"none");

    public function __construct($patronID)
    {
        parent::__construct($patronID);

        $this->populateCheckoutData($patronID);
        $this->sortCheckouts('bibTitle');  // default sort by title.
   }

    protected function populateCheckoutData($patronID)
    {
        parent::populateCheckoutData($patronID);

        // loop through the checkouts array and add the bib title to each element.
        // we have to get the item id from the checkout record and then bib id from the item record.
        include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-bib.php");
        include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-item.php");

        for ($i = 0; $i < count($this->checkouts); ++$i) {
            $lastSlash = strrpos($this->checkouts[$i]['item'],'/');
            $myItemId = substr($this->checkouts[$i]['item'],$lastSlash+1,strlen($this->checkouts[$i]['item']));
            $myItem = new sierraItem($myItemId);
            $myBib = new sierraBib($myItem->getFirstBibId());
            $this->checkouts[$i]['bibId'] = $myItem->getFirstBibId();
            $this->checkouts[$i]['bibTitle'] = $myBib->getBibTitle();
            $this->checkouts[$i]['itemType'] = $myItem->getItemType();
        }
    }

    public function getItemType($index) {
        return $this->checkouts[$index]['itemType'];
    }

    public function getEncoreDeepLink($index) {
        //the url to the encore server is hardcoded here.  someday we may want to make it set by a config value
        $linkString = 'https://' . $this->config->getPacServer() . '/iii/encore/record/C__Rb';
        $linkString .= $this->checkouts[$index]["bibId"];
        return $linkString;
    }

    public function getBibDetailLink($index) {
        $linkString = '/bibdetail.php';
        $linkString .= '?bibid=' . $this->checkouts[$index]["bibId"];
        return $linkString;
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
    function cmpDueDateASC($a, $b) { return strnatcasecmp($a['dueDate'], $b['dueDate']); }
    function cmpDueDateDESC($a, $b) { return strnatcasecmp($b['dueDate'], $a['dueDate']); }
    function cmpItemTypeASC($a, $b) { return strnatcasecmp($a['itemType'], $b['itemType']); }
    function cmpItemTypeDESC($a, $b) { return strnatcasecmp($b['itemType'], $a['itemType']); }
    function cmpCallNumberASC($a, $b) { return strnatcasecmp($a['callNumber'], $b['callNumber']); }
    function cmpCallNumberDESC($a, $b) { return strnatcasecmp($b['callNumber'], $a['callNumber']); }
    function cmpNumberOfRenewalsASC($a, $b) { return strnatcasecmp($a['numberOfRenewals'], $b['numberOfRenewals']); }
    function cmpNumberOfRenewalsDESC($a, $b) { return strnatcasecmp($b['numberOfRenewals'], $a['numberOfRenewals']); }

    // $fieldname:               be sure and pass in the fieldname as returned by the API (it's the array key)
    // $direction:              ASC or DESC
    public function sortCheckouts($fieldname) {
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
                    usort($this->checkouts, array($this, "cmpTitleASC")) :
                    usort($this->checkouts, array($this, "cmpTitleDESC"));
                break;
            }
            CASE 'dueDate' : {
                ($direction == "ASC") ?
                    usort($this->checkouts, array($this, "cmpDueDateASC")) :
                    usort($this->checkouts, array($this, "cmpDueDateDESC"));
                break;
            }
            CASE 'itemType' : {
                ($direction == "ASC") ?
                    usort($this->checkouts, array($this, "cmpItemTypeASC")) :
                    usort($this->checkouts, array($this, "cmpItemTypeDESC"));
                break;
            }
            CASE 'callNumber' : {
                ($direction == "ASC") ?
                    usort($this->checkouts, array($this, "cmpCallNumberASC")) :
                    usort($this->checkouts, array($this, "cmpCallNumberDESC"));
                break;
            }
            CASE 'numberOfRenewals' : {
                ($direction == "ASC") ?
                    usort($this->checkouts, array($this, "cmpNumberOfRenewalsASC")) :
                    usort($this->checkouts, array($this, "cmpNumberOfRenewalsDESC"));
                break;
            }
        }
    }
}
