<?php
//get app core path
require APP . DS . 'app_paths.php';

//app view template pathes
require VIEWS . DS . 'view_paths.php';

//app helper
require APP_HELPERS. DS . 'AppHelper.php';

//app core modules
require APP_CORE . DS . 'AppApi.php';
require APP_CORE . DS . 'AppModel.php';
require APP_CORE . DS . 'AppView.php';
require APP_CORE . DS . 'AppController.php';


//set routing [routing url => array(controller file path, method name in the controller)]
$routingTable = [
    //api
    'api' => array(API . DS . 'ApiMain.php', 'run'),
        
    //home
    '' => array(CONTROLLERS . DS . 'SimpleMvc.php', 'dashboard'),

    //accoutns routings
    'accounts' => array(CONTROLLERS . DS . 'Accounts.php', 'users'),
    'accounts/login' => array(CONTROLLERS . DS . 'Accounts.php', 'login'),
    'accounts/logout' => array(CONTROLLERS . DS . 'Accounts.php', 'logout'),

    //simpleMvc routings
    'simpleMvc' => array(CONTROLLERS . DS .'SimpleMvc.php', 'dashboard'),
    'simpleMvc/dashboard' => array(CONTROLLERS . DS .'SimpleMvc.php', 'dashboard'),

    //redirection for the http status
    404 => array(CONTROLLERS . DS . 'PageExceptions.php', 'notFound')
];

//Create AppStater to start web app
$app = new SimpleMvc\App($routingTable);
$app->run();