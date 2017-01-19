<?php
/*
 * This file is part of the SimpleMvc package.

 * @copyright 2016-2017 Jamie Kim
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleMvc;

/**
 * App Class
 */
final class App
{
    /**
     * global settings
     *
     * @var array
     */
    private $token;
    private $settings;
    private $routingTables;
    
    /**
     * controller, method and parameters
     *
     * @var string
     */
    
    private $routingPath;
    private $controllerClassName;
    private $controllerFile;
    private $method;
    private $args;
    
    /**
     * Constructor : set global app settings.
     */
    public function __construct($routingTable)
    {
        $this->settings = new \AppSettings();
        $this->routingTable = $routingTable;
    }
    
    /**
     * Start the application
     */
    public function run()
    {
        //get parsed uri
        $this->parseUri($this->routingTable);
        
        if(!$this->isRequestValidate()) {
            //redirect to not found page
            $this->redirect(404);
        }

        //create model
        $model = new \AppModel($this->settings);

        //create view
        $view = new \AppView($this->settings);
        
        //create controller
        $controller = new $this->controllerClassName(
            $model,
            $view,
            $this->settings,
            $this->routingPath,
            $this->args
        );

        //run action method and show template
        $method = $this->method;
        return $controller->$method($_REQUEST);
    }

    /**
     * Parse URI to get controller and action
     */
    private function parseUri($routingTable)
    {
        //get parsed uri
        $baseUri = parse_url($this->settings->base_url, PHP_URL_PATH);
        $uriPath = substr(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), strlen($baseUri));

        //get routing info for the uri path
        $routingInfo = $this->getRoutingInfo($uriPath, $routingTable);
        
        //set controller and action and params
        $this->controllerFile = array_shift($routingInfo);
        
        //class name is same as filename of controller
        $this->controllerClassName = pathinfo($this->controllerFile, PATHINFO_FILENAME);
        $this->method = array_shift($routingInfo);
        $this->routingPath = array_shift($routingInfo);
        $this->args = array_shift($routingInfo);
    }
    
    /**
     * Get routing information from the routing tables.
     * 
     * @return array
     */
    private function getRoutingInfo($uriPath, $routingTable)
    {
        $routingInfo = array();
        $foundLength = 0;

        foreach($routingTable as $key => $value) {
            $keyLength = strlen($key);
            $routePath = substr($uriPath, 0, $keyLength);

            //in case of root path
            if(empty($uriPath) && empty($key)) {
                $routingInfo= $this->buildRoutingInfo($key, $value, $uriPath, $args);
                break;
            }
            
            //find first part of the routingTable in uriPath
            if($key == substr($uriPath, 0, $keyLength)) {

                //we need to get the correct routing table which has logest length of key.
                if($foundLength < $keyLength) {
                    $foundLength = $keyLength;
                    $routingInfo = $this->buildRoutingInfo($key, $value, $uriPath, $args);
                }
            }
        }

        return $routingInfo;
    }
    
    /**
     * Build routing information
     *
     * @return bool
     */
    private function buildRoutingInfo($key, $value, $uriPath, $args)
    {
        //set the routing info
        $routingInfo = $value;
        
        //push args at the end of array
        $args = substr($uriPath, strlen($key), strlen($uriPath));
        array_push($routingInfo, $key);
        array_push($routingInfo, $args);
        
        return $routingInfo;
    }
    
    /**
     * Validation for the request
     *
     * @return bool
     */
    private function isRequestValidate()
    {
        $ret = false;
        
        //run action
        if (file_exists($this->controllerFile)) {
            
            //include the controller file
            require $this->controllerFile;
            
            //check method is exist
            $ret = method_exists($this->controllerClassName, $this->method);
        }
        
        if(!$ret) {
            if($this->settings->debug_mode) {
                die($this->controllerFile . ' or ' . $this->controllerClassName . 
                    ':' . $this->method . ' is not found.');
            }          
        }

        return $ret;
    }
    
    /**
     * redirect to not found page
     */
    private function redirect($httpStatusCode)
    {
        $redirect_url = $this->settings->base_url . $httpStatusCode;
        header("Location: $redirect_url");
        exit();
    }
    
    /**
     * save logs for PHP errors if the PhpError is true.
     * if it is debug mode, then we don't need to save the PHP error in the log file.
     */
    public function setPhpErrorMode($logFolder)
    {
        $debug = $this->settings->debug_mode;
        $phpLog = $this->settings->logTypes['PhpError'];
        
        if($debug) {
            $this->setPhpErrorDebugMode();
        } else if($phpLog) {
            $this->setPhpErrorLogMode($logFolder);            
        } else {
            //don't show any errors.
        }                
    }    
    
    /**
     * PHP error log to file
     */    
    private function setPhpErrorLogMode($logFolder)
    {
        //check directory exist
        if(!file_exists($logFolder)) {
            return;
        }

        //get log fileName
        $fileName = date("Y-m-d") . '.log';
        $logPath = $logFolder . DS . $fileName;      

        //set error options
        ini_set('log_errors', 1);
        ini_set('error_log', $logPath);
        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));                 
    }
    
    /**
     * PHP error log to display to browser
     */    
    private function setPhpErrorDebugMode()
    {        
        //set error options            
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));                      
    }       
}