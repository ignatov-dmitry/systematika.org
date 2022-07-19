<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\LeadsModel;
use GKTOMK\Models\MoyklassModel;

class WidgetController extends Controller
{
    public $View = '';

    public function __construct()
    {
        $this->View = new \GKTOMK\Views\IndexView();

    }

    public function main($email = 'string'){
        header('Access-Control-Allow-Origin: *');
        $users = MoyklassModel::getFindUsers(['email' => $email]);
        if(empty($users['users'][0]['id']) or $users['users'][0]['id']<1){
            $this->answerAjax(['userId' => 0]);
        }else{
            $this->answerAjax(['userId' => $users['users'][0]['id']]);
        }
    }

    public function getGo($email = 'string'){

        $users = MoyklassModel::getFindUsers(['email' => $email]);
        if(empty($users['users'][0]['id']) or $users['users'][0]['id']<1){
            $this->View->parseTpl('redirect', false)->parseTpl('main')->output();
            return;
        }else{
            $url = 'https://app.moyklass.com/user/'. $users['users'][0]['id'] . '/joins';
            header("Location: " . $url);
        }
        die();
    }

}