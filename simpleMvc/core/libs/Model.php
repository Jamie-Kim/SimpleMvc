<?php
/*
 * This file is part of the SimpleMvc package.

 * @copyright 2016-2017 Jamie Kim
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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