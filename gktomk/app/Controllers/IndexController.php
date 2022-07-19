<?php

namespace GKTOMK\Controllers;
//use \GKTOMK\Lib\Controller;

//use \GKTOMK\Views\IndexView;
use GKTOMK\Models\HandlerModel;
use GKTOMK\Models\LeadsModel;
use GKTOMK\Models\LogsModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\SyncModel;

class IndexController
{

    public $View = '';


    function __construct()
    {
        session_start();
        if(!empty($_GET['password']))
            $_SESSION['passwords'] = $_GET['password'];
        if(isset($_REQUEST['password']) and $_REQUEST['password'] != CONFIG['admin_password']){
            die('Access error!');
        }
        $this->View = new \GKTOMK\Views\IndexView();

        $this->View->setVar('URL_GK', CONFIG['url_gk']);
    }

    function main()
    {


        $LogsModel = new LogsModel();
        $all_users = $LogsModel->buildLogs();

        // Регистрируем функции, чтобы можно было вызвать их в шаблоне
        $this->View->setVar('Logs',  $LogsModel);
        $this->View->regFunc('GKTOMK\Models\LogsModel::timeFormat');


        $this->View->setVar('LOGS', $all_users);



        // Вызываем шаблонизатор
        $this->View->parseTpl('logs', false)->parseTpl('main')->output();
    }


    public function getSettConn()
    {

        $this->View->parseTpl('settings/connection', false)->parseTpl('main')->output();
    }

    public function getSettSync()
    {

       // SyncModel::getSyncAll();

        $SyncModel = new SyncModel();
       // $SyncModel->createSync('Абонемент 4 занятия, 2800', '1031346', '24747');

        $syncs = $SyncModel->getAllSync();

        var_dump($syncs);
        $this->View->setVar('SYNCS', $syncs);

        $this->View->parseTpl('settings/synchronization', false)->parseTpl('main')->output();
    }

    public function postSettSyncAdd(){
        print_r($_REQUEST);
    }

    public function getSubscriptions()
    {
        $this->View->varTpl('SUBSCRIPTIONS', MoyklassModel::getSubscriptions()['subscriptions']);
	    $this->View->parseTpl('settings/subscriptions', false)->parseTpl('main')->output();
    }

    public function getMkClasses(){
        return json_encode(MoyklassModel::getClasses());
    }

    public function getTest(){

       // $HandlerModel = new HandlerModel();

      //  var_dump($HandlerModel->handle(27));

        //$result = MoyklassModel::getLessons(['classId'=>'104145']);

        //var_dump($result);
        $leads = new LeadsModel();
        $mk_user = $leads->getFindUserByEmail('anekrasov123@mail.ru');

        var_dump($mk_user);
    }

}