<?php
require REPOSITORIES. DS . 'UsersRepo.php';

//
// accounts controller
//
class Accounts extends AppController
{
    //-------------------------------------------------------------------------------------
    // Show web page
    //
    // $param is same as $_REQUESTED
    //-------------------------------------------------------------------------------------

    //show login page
    public function login($param)
    {
        $usersReop = new UsersRepo($this->model, $this->settings);
        $data = $usersReop->loginController($param);
        
        //redirect if user and password are correct
        if($usersReop->isAuthorized()) {
            $this->redirect('simpleMvc/dashboard');
        }
        else {
            $this->view->rendering(LOGIN_PAGE, $data);
        }
        
        Logger::writeline('Debug', 'test');
    }

    public function logout($param)
    {
        session_unset();
        $this->redirect('accounts/login');
    }
}
