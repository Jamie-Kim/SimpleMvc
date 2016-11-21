<?php
//
// pageExceptions controller
//
class pageExceptions extends AppController
{
    //--------------------------------------------------------------------------------------------------------------------
    // Actions
    //--------------------------------------------------------------------------------------------------------------------
    public function notFound($param)
    {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
    }
}