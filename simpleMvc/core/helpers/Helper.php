<?php
/*
 * This file is part of the SimpleMvc package.

 * @copyright 2016 Jamie Kim
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SimpleMvc;

Class Helper
{
    //get request method.
    public static function isPostRequest()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            return true;
        } else {
            return false;
        }
    }        
    
    //combine two paths
    public static function combine($path1, $path2)
    {
        $first = rtrim($path1, DIRECTORY_SEPARATOR);
        $second = ltrim($path2, DIRECTORY_SEPARATOR);
        return $first. DIRECTORY_SEPARATOR . $second;
    }
}