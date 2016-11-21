<?php
/**
 * SimpleMvc
 *
 * @copyright 2016-2017 Jamie Kim
 */
namespace SimpleMvc;

/**
 * Model Class
 */
class Model
{
    /**
     * settings
     */
    protected $settings;
    
    /**
     * database connection
     */
    protected $_db = array();
    
    /**
     * construct
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
    }
    
    /**
     * get selected db handle
     * 
     * @return db handle
     */
    public function db($selector = 'default')
    {
        if (!$this->_db[$selector]) {
            $this->connect($selector);
        }
        
        return $this->_db[$selector];
    }
    
    /**
     * A method to connect to the database
     * 
     * @return db handle
     */
    private function connect($selector)
    {
        $database = $this->settings->databases[$selector];

        //create database connection
        $this->_db[$selector] = new Database(
            $database['host'], 
            $database['name'], 
            $database['user'],
            $database['password'],
            //debug options in app settings
            $this->settings->debug_mode
        );
    }
}