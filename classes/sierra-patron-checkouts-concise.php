<!--

Data retrieval with this class is faster than the expanded class because the constructor of this class
does NOT make repeated calls to the bib and item apis.

-->

<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/sierra-patron-checkouts.php");

class sierraPatronCheckoutsConcise extends sierraPatronCheckouts
{

    public function __construct($patronID)
    {
        parent::__construct($patronID);
   }
}
