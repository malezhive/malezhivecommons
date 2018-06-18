<?php
/**
* This class manage application logs.
*
* @author Didelot Guillaume <gdidelot@live.fr>
* @version 1.0
* @package MalezHive\Commons
* @subpackage Commons
*/
namespace MalezHive\Commons;

/**
* Logger class definition. This class manage application logs.
*
* @method Logger Singleton()
* @method void Debug($message)
* @method void Info($message)
* @method void Warning($message)
* @method void Error($message)
*/
class Logger
{
    /**
    * @var string $RootPath The root path for all log files
    */
    private static $RootPath;
    
    /**
    * @var Logger $instance The session instance
    */
    private static $instance;

    /**
    * The singleton instance
    *
    * @return A new Logger instance
    */
    public static function Singleton()
    {
        if (!isset(self::$instance) || self::$FilePath == null || self::$FilePath == "") {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }
    
    /**
    * Default constructor
    */
    private function __construct()
    {
        self::$RootPath = isset(CORE_DIR) ? CORE_DIR . "../Logs" ? "Logs";
        if (file_exists(self::$RootPath) == false) {
            mkdir(self::$RootPath);
        }
    }
    
    /**
    * Debug trace method
    *
    * @param string $message The message to write on the log file
    */
    public static function Debug($message)
    {
        $trace = debug_backtrace();
        $caller = isset($trace[1]) ? $trace[1] : 'unknown';
        $function = (isset($caller['function'])) ? $caller['function'] : 'unknown';
        $class = (isset($caller['class'])) ? $caller['class'] : 'unknown';
        $logger = \Logger::getLogger($class);
        $address = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'localhost';
        $logger->debug($address . ' ' . $message);
    }
    
    /**
    * Info trace method
    *
    * @param string $message The message to write on the log file
    */
    public static function Info($message)
    {
        $trace = debug_backtrace();
        $caller = isset($trace[1]) ? $trace[1] : 'unknown';
        $function = (isset($caller['function'])) ? $caller['function'] : 'unknown';
        $class = (isset($caller['class'])) ? $caller['class'] : 'unknown';
        $logger = \Logger::getLogger($class);
        $address = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'localhost';
        $logger->info($address . ' ' . $message);
    }

    /**
    * Warning trace method
    *
    * @param string $message The message to write on the log file
    */
    public static function Warning($message)
    {
        $trace = debug_backtrace();
        $caller = isset($trace[1]) ? $trace[1] : 'unknown';
        $function = (isset($caller['function'])) ? $caller['function'] : 'unknown';
        $class = (isset($caller['class'])) ? $caller['class'] : 'unknown';
        $logger = \Logger::getLogger($class);
        $address = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'localhost';
        $logger->warn($address . ' ' . $message);
    }
    
    /**
    * Error trace method
    *
    * @param string $message The message to write on the log file
    */
    public static function Error($message)
    {
        $trace = debug_backtrace();
        $caller = isset($trace[2]) ? $trace[2] : 'unknown';
        $function = (isset($caller['function'])) ? $caller['function'] : 'unknown';
        $class = isset($caller['class']) ? $caller['class'] : "unknown";
        $logger = \Logger::getLogger($class);
        $address = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'localhost';
        $logger->error($address . ' ' . $class . '.' . $function .': '. $message);
    }
}
?>