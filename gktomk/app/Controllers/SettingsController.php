<?php

namespace GKTOMK\Controllers;
//use \GKTOMK\Lib\Controller;

use GKTOMK\Models\SyncModel;

class SettingsController
{

    public $View = '';


    function __construct()
    {
        session_start();
        if (!empty($_GET['password']))
            $_SESSION['password'] = $_GET['password'];

        if (empty($_SESSION['password']) or !in_array($_SESSION['password'], CONFIG['admin_password'])) {
            die('Access error! ' . $_REQUEST['password']);
        }
        $this->View = new \GKTOMK\Views\IndexView();

        $this->View->setVar('PASSWORD', $_REQUEST['password']);
        $this->View->setVar('URL_GK', CONFIG['url_gk']);
    }


    public function getConn()
    {

        $this->View->parseTpl('settings/connection', false)->parseTpl('main')->output();
    }

    public function getSync()
    {

        // SyncModel::getSyncAll();


        // $SyncModel->createSync('Абонемент 4 занятия, 2800', '1031346', '24747');


        //var_dump($SyncModel->getSync(['gk_offer' => '1031346', 'mk_sub' => '24747']));
        // $this->View->setVar('SYNCS', $syncs);

        $this->View->parseTpl('settings/synchronization', false)->parseTpl('main')->output();
    }

    public function getSyncListAjax()
    {
        $SyncModel = new SyncModel();
        $syncs = $SyncModel->getAllSync();
        return json_encode($syncs);
    }

    public function postSyncAdd()
    {
        //print_r($_REQUEST);
        $sync = $_POST['sync'];

        if (isset($sync)) {

            if (empty($sync['program'])) {
                return json_encode(['code' => 'error', 'result' => 'program empty']);
            } elseif (empty($sync['gk_offer'])) {
                return json_encode(['code' => 'error', 'result' => 'gk_offer empty']);
            } elseif (empty($sync['mk_sub'])) {
                return json_encode(['code' => 'error', 'result' => 'mk_sub empty']);
            }

            $SyncModel = new SyncModel();

            if (!empty($SyncModel->getSync(['gk_offer' => $sync['gk_offer'], 'mk_sub' => $sync['mk_sub']]))) {
                return json_encode(['code' => 'error', 'result' => 'sync is duplicate']);
            }


            $SyncModel->createSync($sync['program'], $sync['gk_offer'], $sync['mk_sub'], $sync['demo']);
            return json_encode(['code' => 'success']);
        }
    }

    public function postSyncEdit()
    {
        print_r($_POST);
        $SyncModel = new SyncModel();
        $SyncModel->editSync($_POST['sync']);
    }

    public function deleteSync($id)
    {
        $SyncModel = new SyncModel();
        $SyncModel->delSync($id);
    }

    public function getSubscriptions()
    {
        $this->View->varTpl('SUBSCRIPTIONS', MoyklassModel::getSubscriptions()['subscriptions']);
        $this->View->parseTpl('settings/subscriptions', false)->parseTpl('main')->output();
    }

    public function getTest()
    {

        $SyncModel = new SyncModel();
        $res = $SyncModel->getSync(['gk_offer'=>'1031346']);
        var_dump($res);
    }

}