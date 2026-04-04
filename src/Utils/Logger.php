<?php

namespace App\Utils;

/**
 * Simple file-based logger for debugging and error tracking.
 * Writes timestamped entries to separate log files under /Logs/.
 */
class Logger
{
    private static string $logPath = __DIR__ . '/../../Logs/';

    /**
     * Appends a timestamped entry to the specified log file.
     * Arrays and objects are serialised as pretty-printed JSON.
     *
     * @param string       $filename Log file name without extension (e.g. 'auth', 'db_errors').
     * @param mixed        $data     Message string, array, or object to log.
     * @return void
     */
    public static function log(string $filename, $data): void
    {
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0777, true);
        }

        $date = date('Y-m-d H:i:s');

        $message = (is_array($data) || is_object($data))
            ? json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            : $data;

        $logEntry = "[$date] " . $message . PHP_EOL . str_repeat("-", 30) . PHP_EOL;

        file_put_contents(self::$logPath . $filename . '.log', $logEntry, FILE_APPEND);
    }
}
