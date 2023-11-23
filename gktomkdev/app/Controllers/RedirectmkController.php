<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\LeadsModel;
use GKTOMK\Models\MoyklassModel;

class RedirectmkController
{
    public $View = '';

    public function __construct()
    {
        $this->View = new \GKTOMK\Views\IndexView();

    }

    public function main($email = 'string'){

        $users = MoyklassModel::getFindUsers(['email' => $email]);
        if(empty($users['users'][0]['id']) or $users['users'][0]['id']<1){
            $this->View->parseTpl('redirect', false)->parseTpl('main')->output();
        }
       $url = 'https://app.moyklass.com/user/'. $users['users'][0]['id'] . '/joins';
        header("Location: " . $url);
        exit;
        echo 'Ваш ID: '.$url;
    }

}