<?php
//api result text
define('RT_TRUE', '1');
define('RT_FALSE', '0');

/**
 * ApiMain class
 * {API URL}/{resource}/{command}?{parameters}
 * e.g https://ransomgaurd.net/app/api/users/get_is_user_registered?parameters
 * response as JSON : ["data":{data},"status":{status},"message":{message}] 
 */   
final class ApiMain extends AppApi
{    
    public function run($param)
    {        
        //api routing table [resource => [command => [method(POST,GET,PUT,PATCH,DELETE), Resource Path, action)]]
        $apiTable = [
            'users' => [
                'get_is_user_registered' => ['GET', RESOURCES . DS .'RgUserApiRes.php', 'isRegisteredUser'],
                'set_user_registry' => ['POST', RESOURCES . DS .'RgUserApiRes.php', 'setUser']          
                
            ],
            
            'files' => [
                'get_file_info' => ['GET', RESOURCES . DS .'RgFileApiRes.php', 'getFileInfo'],
                'set_user_file_info' => ['POST', RESOURCES . DS .'RgFileApiRes.php', 'setUserFileInfo']
            ],            
        ];
        
        //check validation, if not exit it.
        //$this->checkValidation($param);

        //excute api and get result
        $this->cmdApiResponse($apiTable, $param);
    }
    
    //--------------------------------------------------------------------------------------------------------
    // Validations
    //--------------------------------------------------------------------------------------------------------
    private function checkValidation($param) 
    {
        $response = array();
        
        if(!$this->ApiValidation($param)) {
            $this->flushResponse($this->setStatus($response, 401, "Validation error"));  
        }
    }
    
    private function ApiValidation($param) 
    {
        //api keys
        $apiKey = $this->settings->apiKey;
        $signKey = $this->settings->signKey;
  
        //security key isn't set
        if (empty($param['_ApiKey']) ||
            empty($param['_Signature']) ||
            empty($param['_Timestamp']) ) {
            return false;
        }
        
        //check api key
        if ($param['_ApiKey'] != $apiKey) {
            return false;
        }

        //allow delay for 60 sec only
        date_default_timezone_set('UTC');
        $ts = time() - strtotime($param['_Timestamp']);
        if ($ts < -60 || $ts > 60) {
            return false;
        }

        //set parameters
        $args = array();
        ksort($param);
        foreach ($param as $k => $v) {
            if ($k != '_Signature' && $k != 'PHPSESSID') {
                array_push($args, $k . '=' . rawurlencode($v));
            }
        }

        //check the signature
        $msg = '?' . join('&', $args);
        $sign = hash_hmac('sha1', $msg, $signKey);
        if ($param['_Signature'] != $sign) {
            return false;
        }

        //success
        return true;
    }        
}