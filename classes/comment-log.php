<?php
class commentLog {

    // some constants
    private static $delimiter = ',';
    private static $logFilename = 'logs/comment-log.csv';


    public function __construct() {

        // create a logs folder if it doesn't already exist
        if (!file_exists('logs')) {
            mkdir('logs', 0777, true);
        }

        // if there's no log file yet, create it and add the first line (a static text header)
        if (!file_exists(self::$logFilename)) {
            $logHeaderStr = 'Date and Time,Name,Email,Reply,Comment';
            file_put_contents(self::$logFilename, $logHeaderStr . PHP_EOL);
        }
    }

    public function writeLogEntry($name,$email,$reply,$comment)
    {
        $transactionDate = date('Y-m-d H:i:s');
        $replyString = ($reply == 1 ? 'Yes' : 'No');

        $logEntry =
            $transactionDate . self::$delimiter .
            '"' . $name . '"' . self::$delimiter .
            $email . self::$delimiter .
            $replyString . self::$delimiter .
            '"' . $comment . '"' .
            PHP_EOL;

        file_put_contents(self::$logFilename, $logEntry, FILE_APPEND);
    }
}
