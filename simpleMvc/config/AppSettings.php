<?php
/**
 * AppSettings
 * 
 * It is global settings usable in controller.
 * Please don't add new member variable since it can be related to the core sources and it can be updated.
 * Additional APP settings should be in CustomSettings.
 */
class AppSettings extends CustomSettings
{
    /**
     * debug mode
     */
    public $debug_mode = false;
    
    /**
     * base URL, slash is needed at the end of URL
     */
    public $base_url = 'http://centos7/simpleMvc/';

    /**
     * application title
     */
    public $app_title = 'SimpleMvc PHP';

    /**
     * database configurations
     */
    //set routing [alias name to select => array(host, name, user , password, db timezon)]
    public $databases = [
            //default db connection
            'default' => array(
                'host' => 'localhost',
                'name' => 'simpleMvc',
                'user' => 'simpleMvc',
                'password' => '123456aa',
                'time_zone' => 'America/Los_Angeles'
            )
    ];

    /**
     * name to use in template
     */
    public $app_title_view_name = 'appTitle';
    public $base_url_view_name = 'appBaseUrl';
    public $style_url_view_name = 'appViewUrl';

    /**
     * default theme path is app/views/default
     */
    public $theme = 'default';
        
    /**
     * save logs by log type
     * true : write log , 
     * false : skip log.
     * 
     * The log file will be saved as 'app/temp/logs/[date].log' 
     * 
     * e.g. Logger::writeLine('LogTypeDebug', 'message') in application.
     */
    public $logTypes = [
        //custom debug logs
        'Api' => false,

        //security logs
        'Security' => true,        
        
        //general log types
        'Debug' => true,
        'Info' => true,
        'Warning' => true,
        'Error' => true,

         //php error log option
        'PhpError' => true
    ];    
}