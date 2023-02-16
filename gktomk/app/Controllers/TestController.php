<?php


namespace GKTOMK\Controllers;



use GKTOMK\Models\Wazzup24Model;

class TestController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getMessage(){
        $wazzup = new Wazzup24Model();
        $wazzup->sendMessage();
    }
}