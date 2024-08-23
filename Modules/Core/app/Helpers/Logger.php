<?php

namespace Modules\Core\Helpers;

use Log;

class Logger
{
    public static function getChannel($channel)
    {
        if (!empty($channel) && $channel != null) {
            return $channel;
        }
        return config('logging.default');
    }

    /**
     * Log an error
     * @param Exception/string $exception
     * @param string $channel
     */
    public static function error($exception, $channel = null)
    {
        $host = $_SERVER['HTTP_HOST'] ?? null;
        if ($exception instanceof \Exception) {
            Log::channel(self::getChannel($channel))->error($host.": ".$exception->getMessage() . ' at ' . $exception->getFile() . ':' . $exception->getLine());
        } else {
            Log::channel(self::getChannel($channel))->error($host.": ".$exception);
        }
    }
    
    /**
     * Log a warning
     * @param Exception/string $exception
     * @param string $channel
     */
    public static function warning($exception, $channel = null)
    {
        $host = $_SERVER['HTTP_HOST'] ?? null;
        Log::channel(self::getChannel($channel))->warning($host.": ".$exception);
    }
    
    /**
     * Log an information
     * @param Exception/string $exception
     * @param string $channel
     */
    public static function info($exception, $channel = null)
    {
        $host = $_SERVER['HTTP_HOST'] ?? null;
        Log::channel(self::getChannel($channel))->info($host.": ".$exception);
    }
    
    /**
     * Log a debug
     * @param Exception/string $exception
     * @param string $channel
     */
    public static function debug($exception, $channel = null)
    {
        $host = $_SERVER['HTTP_HOST'] ?? null;
        Log::channel(self::getChannel($channel))->debug($host.": ".$exception);
    }
}
