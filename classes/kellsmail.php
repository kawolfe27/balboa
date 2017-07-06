<?php

include_once($_SERVER['DOCUMENT_ROOT'] . "/classes/config.php");

class kellsmail
{
    protected $config;

    private $contentType = "text/html; charset=ISO-8859-1";  /* Set the email type to HTML */
    private $recipient = "";
    private $subject = "";
    private $message = "";
    private $headers = "";
    private $success = false;

    function __construct($recipient, $emailSubject, $emailMessage) {

        $this->config = new config();

        $this->headers = "From: " . $this->config->getCommentSender() . "\r\n";
        $this->headers .= "Reply-To: " . $this->config->getReplyTo() . "\r\n";
        $this->headers .= "Content-Type: " . $this->contentType . "\r\n";
        $this->headers .= "X-Mailer: PHP/" . phpversion();

        $this->recipient = $recipient;
        $this->subject = $emailSubject;
        $this->message = $emailMessage;
    }

    public function sendMail() {
        $this->success = mail($this->recipient, $this->subject, $this->message, $this->headers);
    }

    public function getRecipient() {
        return $this->recipient;
    }

    public function emailSuccessful() {
        return $this->success;
    }
}