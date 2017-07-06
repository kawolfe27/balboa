<?php


class ideas
{
    private $ideaData = array(); /* This will be an array of idea text strings */

    public function __construct() {
        /* read the json file */
        $this->ideaData = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/json/ideas.json"),true);
    }

    private function rewriteDataFile() {
        $fp = fopen($_SERVER['DOCUMENT_ROOT'] . '/json/ideas.json', 'w');
        $jsonIdeas = json_encode($this->ideaData);
        fwrite($fp, $jsonIdeas);
        fclose($fp);
    }

    public function getAnIdea($index) {
        return $this->ideaData[$index];
    }

    public function addAnIdea($ideaText) {
        if (count($this->ideaData) > 0) {
            array_unshift($this->ideaData,$ideaText);  // adds the element to the beginning of the array
        } else {
            $this->ideaData = array($ideaText);
        }
        $this->rewriteDataFile();
    }

    public function deleteAnIdea($index) {
        unset($this->ideaData[$index]);
        $this->ideaData = array_values($this->ideaData);  // reindex the array
        $this->rewriteDataFile();
    }

    public function getTotalIdeas() {
        return count($this->ideaData);
    }
}