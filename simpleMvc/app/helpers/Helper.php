<?php

Class Helper
{
    public static function isPostRequest()
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            return true;
        } else {
            return false;
        }
    }
}