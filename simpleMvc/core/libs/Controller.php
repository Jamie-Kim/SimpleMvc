<?php
/**
 * SimpleMvc
 *
 * @copyright 2016-2017 Jamie Kim
 */
namespace SimpleMvc;

/**
 * Controller Class
 */
class Controller
{
    protected $model;
    protected $view;
    protected $settings;
    protected $routingPath;
    protected $args;

    /**
     * construct
     */
    public function __construct($model, $view, $settings, $routingPath, $args = null)
    {
        $this->model = $model;
        $this->view = $view;
        $this->settings = $settings;
        $this->routingPath = $routingPath;
        $this->args = $args;

        //check arguments valication
        if($args != null) {
            $this->argsValidation($args);
        }
    }

    /**
     * redirect to routing path, it will call exit();
     */
    public function redirect($routingPath)
    {
        $redirect_url = $this->settings->base_url . $routingPath;
        header("Location: $redirect_url");
        exit();
    }
    
    /**
     * argument security check.
     */
    public function argsValidation($args)
    {
        if (!preg_match('/^[\/A-Za-z0-9-_]+$/', $args)) {
            $this->redirect('');
        }
    }
}