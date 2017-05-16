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
 * API Class
 */
class Api extends Controller
{
    private $httpStatus = [
            200 => 'API OK',
            400 => 'API Bad Request',
            401 => 'API Unauthorized',
            404 => 'API Not Found',
            405 => 'API Method Not Allowed',
            500 => 'API Internal Server Error',  
        ];
    
    protected $command;
    protected $item;
    protected $method;
    
    public function __construct($model, $view, $settings, $routingPath, $args = null)
    {
        parent::__construct($model, $view, $settings, $routingPath, $args);
        
        $this->setApiInfo($args);
    }
    
    /**
     * API with command mode, 
     * {API URL}/{resource}/{command}?{parameters}
     * e.g http://simplemvc.com/app/api/users/get_is_user_registered?parameters
     * Response as JSON : ["data":{data},"status":{status},"message":{message}] 
     * API routing table : [resource => [command => [method(POST,GET,PUT,PATCH,DELETE), Resource Path, action)]]
     */ 
    protected function cmdApiResponse($apiTable, $param)
    {
        //set debug mode false to avoid unexpected response.        
        $this->settings->debug_mode = false;        
        
        //respoinse to send to user as Json data type
        $response = array();
        
        //set resource,command and http method
        $this->setApiInfo($this->args);        
        
        //http request method
        $httpMethod = array_shift($apiTable[$this->resource][$this->command]);
        
        //resouce path and action
        $resourcePath = array_shift($apiTable[$this->resource][$this->command]);
        $action = array_shift($apiTable[$this->resource][$this->command]);
               
        //class name is same as filename of controller
        $className = pathinfo($resourcePath, PATHINFO_FILENAME);       
        
        //check requested method
        if($this->method != $httpMethod){
            $this->flushResponse($this->setStatus($response, 400));        
        }        
        
        //check resource and mehtod
        if(empty($className) || empty($action)) {
            $this->flushResponse($this->setStatus($response, 404));   
        }

        //include the resouce file
        require $resourcePath;

        //create api resouce
        $resource = new $className(
            $this->model,
            $this->view,
            $this->settings,
            $this->routingPath,
            $this->args
        );

        //check the method is exist in the class
        if(!method_exists($resource, $action)) {
            $this->flushResponse($this->setStatus($response, 404, 'Not found ' . $action . ' in ' . $className));           
        }        
        
        //run rmethod
        $response['data'] = $resource->$action($param);
        $response['query'] = $_SERVER['QUERY_STRING'];

        //print json encoded response
        $this->flushResponse($this->setStatus($response, 200));
    }

    /**
     * API with Restful mode, 
     * {API URL}/{resource}/{item}?{parameters}
     * e.g {GET}http://simplemvc.com/app/api/users/123?parameters
     * Response as JSON : ["data":{data},"status":{status},"message":{message}]      * 
     * API routing table : [resource => method(POST,GET,PUT,PATCH,DELETE) => [Resource Path, action)]
     */       
    protected function restApiResponse($apiTable, $param)
    {
        //set debug mode false to avoid unexpected response.        
        $this->settings->debug_mode = false;

        //response to user as Json data type
        $response = array();
                                
        //set resource,command and http method
        $this->setApiInfo($this->args);                
        
        if(empty($apiTable[$this->resource][$this->method])) {
            $this->flushResponse($this->setStatus($response, 404, 'Resource not found'));
        }
            
        //resouce path and action
        $resourcePath = array_shift($apiTable[$this->resource][$this->method]);
        $action = array_shift($apiTable[$this->resource][$this->method]);

        //class name is same as filename of controller
        $className = pathinfo($resourcePath, PATHINFO_FILENAME);       

        //check resource and mehtod
        if(empty($className) || empty($action)) {
            $this->flushResponse($this->setStatus($response, 404));   
        }

        //include the resouce file
        require $resourcePath;

        //check the method is exist in the class            
        if(!method_exists($className, $action)) {
            $this->flushResponse($this->setStatus($response, 404, 'Not found ' . $action . ' in ' . $className));           
        }

        //create resource
        $resource = new $className(
            $this->model,
            $this->view,
            $this->settings,
            $this->routingPath,
            $this->args
        );        

        //run rmethod
        $response['data'] = $resource->$action($param);
        $response['query'] = $_SERVER['QUERY_STRING'];

        //print json encoded response
        $this->flushResponse($this->setStatus($response, 200));
    }
           
    private function setApiInfo($args)
    {
        $trimedArgs = ltrim($args, '/');
        $apiInfo = explode('/', $trimedArgs);
        
        //set Api info
        $this->resource = array_shift($apiInfo);
        $this->item = array_shift($apiInfo);
        $this->command = $this->item;        
        $this->method = $_SERVER['REQUEST_METHOD'];
    }        

    protected function setStatus($response, $statusCode, $message = null) 
    {
        if(empty($message)) {
            $message = $this->httpStatus[$statusCode];
        }
        
        $response['status'] = $statusCode;
        $response['message'] = $message;

       return $response;
    }
    
    protected function flushResponse($response) {
        echo json_encode($response);
        exit();
    }
}
