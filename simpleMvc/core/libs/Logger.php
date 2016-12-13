<?php
/*
 * This file is part of the SimpleMvc package.

 * @copyright 2016-2017 Jamie Kim
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleMvc;

class Logger
{    
    /**
     * @var directory path to save log
     */
    private static $path;
    
    /**
     * @var logTypes array which is set in appSettings.
     */
    private static $logTypes;
    
    /**
     * @var singleton instance handle.
     */
    private static $logger;

    public function __construct($path, $logTypes)
    {
        self::$path = $path;
        self::$logTypes = $logTypes;
    }

    /**
     * write log to file
     */
    public static function writeLine($logType, $message, $context = array(), $dir = LOGS)
    {
        //create singleton instance
        self::createInstance();
        
        //check log type in the settings
        if (array_key_exists($logType, self::$logTypes)) {
            if(self::$logTypes[$logType]) { 
                $logString = self::$logger->setLogFormat($logType, $message, $context);
                self::$logger->fileWrite($logString);
            }
        }
    }

    /**
     * enable log write by log type
     */
    public static function setEnable($logType)
    {   
        self::createInstance();
        self::$logTypes[$logType] = true;
    }
    
    /**
     * disable log write by log type
     */ 
    public static function setDisable($logType)
    {
        self::createInstance();
        self::$logTypes[$logType] = false;
    }       
    
     /**
     * write log to file
     */
    private static function createInstance($dir = LOGS)
    {
        if (!self::$logger) {
            $settings = new \AppSettings();
            self::$logger = new Logger($dir, $settings->logTypes);
        }
    }  
    
    /**
     * build log format
     */
    private function setLogFormat($logType, $message, $context)
    {    
        //12:21:99 [fileName.php:line][Debug] : message
        //[context]  
        $time = date("H:i:s");
   
        //get caller filename and line
        $backtrace = debug_backtrace();
        $file = basename($backtrace[1]['file']) . ':' . $backtrace[1]['line'];

        //set format [time][type][filename:line] : message
        $logFormat = sprintf("[%s][%s][%s] : %s\n",$time, $logType, $file, $message);
        
        //set context values
        if(count($context)) {
            $logFormat .= str_replace('array (', '(', var_export($context, true)) . PHP_EOL;
        }
        
        return $logFormat;
    }
    
    /**
     * write log to file
     */
    private function fileWrite($logString)
    {   
        //check directory exist
        if(!file_exists(self::$path)) {
            return;
        }
        
        //get fileName
        $fileName = date("Y-m-d") . '.log';
        $logPath = self::$path . DS . $fileName;
        
        //open file to write only mode. create new file if the file doesn't exist.
        $file = fopen($logPath, "a") or die($logPath . " could not be opened. Check permissions.");
        fwrite($file, $logString);     
        fclose($file);
    }
}