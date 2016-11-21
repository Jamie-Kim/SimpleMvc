<?php
/**
 * SimpleMvc
 *
 * @copyright 2016-2017 Jamie Kim
 */
namespace SimpleMvc;

/**
 * Api Class
 */
class Api extends Controller
{
    private $httpStatus = array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
    );
    
    protected $resource;
    protected $item;
    protected $method;
    
    public function __construct($model, $view, $settings, $routingPath, $args = null)
    {
        parent::__construct($model, $view, $settings, $routingPath, $args);
        
        //set resource,item and httpverb
        $this->setApiInfo($args);
    }
    
    protected function apiResponse($apiTable, $param)
    {
        if(!empty($apiTable[$this->resource][$this->method])) {
            
            //get resouce method
            $api = $apiTable[$this->resource][$this->method];
            
            //run the method
            $repose = $api[0]->$api[1]($param, $this->item);
            
            //print json encoded response
            $this->flushResponse($repose);
        }else{
            
            //resource not found eror
            $this->flushResponse($this->setStatus($response, 404, 'Resource not found'));
        }
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
    
    private function setApiInfo($args)
    {
        $trimedArgs = ltrim($args, '/');
        $apiInfo = explode('/', $trimedArgs);
        
        //set Api info
        $this->resource = array_shift($apiInfo);
        $this->item = array_shift($apiInfo);
        $this->method = $_SERVER['REQUEST_METHOD'];
    }
    
    private function flushResponse($response) {
    
        echo json_encode($response);
        exit();
    }
}