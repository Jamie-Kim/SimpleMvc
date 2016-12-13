<?php
//
// pageExceptions controller
//
class PageExceptions extends AppController
{
    //--------------------------------------------------------------------------------------------------------------------
    // Actions
    //--------------------------------------------------------------------------------------------------------------------
    public function notFound($param)
    {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    }
}