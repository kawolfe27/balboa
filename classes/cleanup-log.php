<?php
class cleanupLog {

    // some constants
    private static $delimiter = ',';
    private static $logFilename = 'cleanup-log.csv';
    public static $logHeaderStr = 'Date and Time,Message';

    private $transactionDate;

    public function __construct($ttlDeleted) {

        // if there's no log file yet, create it and add the first line (a static text header)
        if (!file_exists(self::$logFilename)) {
            file_put_contents(self::$logFilename, self::$logHeaderStr . PHP_EOL);
        }

        $this->transactionDate = date('Y-m-d H:i:s');
        $this->ttlDeleted = $ttlDeleted;
    }

    public function writeLogEntry()
    {
        $logEntry =
            $this->transactionDate . self::$delimiter .
            $this->ttlDeleted . ' cache file(s) deleted' .
            PHP_EOL;

        file_put_contents(self::$logFilename, $logEntry, FILE_APPEND);
    }
}
