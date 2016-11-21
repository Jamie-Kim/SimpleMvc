<?php
require REPOSITORIES. DS . 'UsersRepo.php';

//
// main controller
//
class SimpleMvc extends AppController
{
    //-------------------------------------------------------------------------------------
    // Construct - We can set authorization to access this controller.
    //
    // $settings : settings in AppSettings.php and CustomSettings.php.
    // $routingPath : path like /base/SimpleMvc/dashboard.
    // $args : additional propertise at the end of URL.
    //-------------------------------------------------------------------------------------
    public function __construct($model, $view, $settings, $routingPath, $args = null)
    {
        parent::__construct($model, $view, $settings, $routingPath, $args);

        $usersReop = new UsersRepo($this->model, $this->settings);
        if(!$usersReop->isAuthorized()) {

            //go to login page
            $this->redirect('accounts/login');
        }
    }

    //-------------------------------------------------------------------------------------
    // Show web page
    //
    // $param is same as $_REQUESTED
    //-------------------------------------------------------------------------------------
    public function dashboard($param)
    {
        $data = $this->dashboardController($param);
        $this->view->rendering(MAIN_PAGE, $data, MAIN_HEADER, MAIN_FOOTER);
    }
    
    //-------------------------------------------------------------------------------------
    // data controller
    //-------------------------------------------------------------------------------------
    private function dashboardController($param)
    {
        $data['loggedName'] = $_SESSION['userName'];
        $data['loggedId'] = $_SESSION['userId'];
        
        return $data;
    }
}
