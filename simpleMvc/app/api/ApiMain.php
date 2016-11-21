<?php
//
// accounts controller
//
class ApiMain extends AppApi
{
    public function run($param)
    {
        //set routing [resouce => [method(POST,GET,PUT,PATCH,DELETE), callback)]
        $apiTable = [
            'users' => 
                [
                    'GET' => array(new apiUsers(), 'getUserInfo'),
                    'POST' => array(new apiUsers(), 'createUserInfo'),
                    'PUT' => array(new apiUsers(), 'updateUserInfo'),
                    'DELETE' => array(new apiUsers(), 'delUserInfo'),
                ]
        ];
        
        //excute api and get result
        $error = $this->apiResponse($apiTable, $param);
    }
}

class apiUsers extends AppApi
{
    public function getUserInfo($param, $item)
    {
        //set status
        $response['user'] = 'Jamie Kim';
        $response = $this->setStatus($response, 200);
        return $response;
    }
    
    public function createUserInfo($param, $item)
    {
        $response['data'] = 'createUserInfo';
        $response = $this->setStatus($response, 201);
        return $response;
    }
    
    public function updateUserInfo($param, $item)
    {
        $response['data'] = 'updateUserInfo';
        $response = $this->setStatus($response, 200);
        return $response;
    }
    
    public function delUserInfo($param, $item)
    {
        $response['data'] = 'delUserInfo';
        $response = $this->setStatus($response, 405);
        return $response;
    }
}