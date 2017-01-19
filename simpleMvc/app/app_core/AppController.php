<?php
/**
 * AppController
 */
class AppController extends SimpleMvc\Controller
{
    public function __construct($model, $view, $settings, $routingPath, $args = null)
    {
        parent::__construct($model, $view, $settings, $routingPath, $args);
    }
    
    protected function isAuthorized()
    {            
        return !empty($_SESSION['user']);
    }
    
    protected function checkAuthorizeAndRedirtect($routingPath = null)
    {    
        if(!$this->isAuthorized()) {
            
            //set default routing path if it is not authorized.
            if(empty($routingPath)) {
                $routingPath = 'login';            
            }
            
            $this->redirect($routingPath);
        }
    }    
    
    //--------------------------------------------------------------------------------------------------------------------
    // utils
    //--------------------------------------------------------------------------------------------------------------------
    protected function sigShort($input)
    {
        $length = 20;
    
        $trimmed_text = substr($input, 0, $length) . '...';
    
        return $trimmed_text;
    }
    
    protected function setSearch(&$param_search, &$session_search)
    {
        $search = '';
    
        if(isset($param_search)) {
            $session_search = $param_search;
        }
    
        if(isset( $session_search)) {
            $search = $session_search;
        }

        $param_search = $search;

        return $search;
    }       
}