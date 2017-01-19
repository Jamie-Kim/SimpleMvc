<?php
//
// User's repository
//
Class UsersRepo
{
    private $model;
    private $settings;
    private $viewData;
    
    public function __construct($model, $settings)
    {
        $this->model = $model;
        $this->settings = $settings;
    }
    
    public function loginController($param)
    {
        $data = array();
        
        if($this->isLoginValidated($param)) {
            
            //get userInfo from database
            $userInfo = $this->getUserInfo($param['id'], $param['password']);
            
            //testing code without using database connection
            $_SESSION['authorized'] = 'admin';
            $_SESSION['userName'] = 'SimpleMvc Php';
            $_SESSION['userId'] = $param['id'];
        }

        return $this->viewData;
    }
    
    public function isAuthorized()
    {
        if($_SESSION['authorized'] == 'admin') {
            return true;
        } else {
            return false;
        }
    }

    public function isLoginValidated($param)
    {
        $ret = false;
        
        if(AppHelper::isPostRequest()) {
            //check the id and password
            if(!empty($param['id']) && !empty($param['password'])) {
                $ret = true;
            }else{
                $this->viewData['error_show'] = 'block';
                $this->viewData['error'] = 'Invalid ID and Password.';
            }
        } 
        //get request
        else {
            $this->viewData['error_show'] = 'none';
            $this->viewData['error'] = '';
        }
        
        return $ret;
    }
    
    //-------------------------------------------------------------------------------------
    // SQL Queries
    //-------------------------------------------------------------------------------------
    public function getUserInfo($id, $pw) 
    {
        $query = "SELECT * FROM ".$this->model->usersTable . " ";
        $query .= "WHERE id ='$id' AND password = '$pw' LIMIT 1";
        
        //excute the query
        /*
        $this->model->db()->query($query);
        return $this->model->db()->single();
        */
    }
    
}