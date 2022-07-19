<?php

namespace GKTOMK\Controllers;
//use \GKTOMK\Lib\Controller;

use GKTOMK\Models\DB;
use GKTOMK\Models\GroupsModel;
use GKTOMK\Models\HomeworklinksModel;
use GKTOMK\Models\HomeworkModel;
use GKTOMK\Models\SyncModel;
use GKTOMK\Models\ZoomaccountsModel;
use GKTOMK\Models\ZoomModel;

class SettingsController extends Controller
{

    public $View = '';


    function __construct()
    {
        /*session_start();
        if (!empty($_GET['password']))
            $_SESSION['password'] = $_GET['password'];

        if (empty($_SESSION['password']) or !in_array($_SESSION['password'], CONFIG['admin_password'])) {
            die('Access error! ' . $_REQUEST['password']);
        }*/
        parent::__construct();
        $this->Member->is_auth();

        // Устанавливаем уровень доступа
        $this->Member->isAccess(1, true);
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


            $SyncModel->createSync($sync['program'], $sync['gk_offer'], $sync['mk_sub'], $sync['demo'], $sync['individual']);
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
        $res = $SyncModel->getSync(['gk_offer' => '1031346']);
        var_dump($res);
    }

    public function getPrograms()
    {
        $this->View->parseTpl('settings/programs', false)->parseTpl('main')->output();
    }

    public function getProgramsAjax()
    {
        $GroupsModel = new GroupsModel();
        /* $GroupsModel->createProgram([
             'name' => 'Олимпиадная математика',
             'shortname' => 'ОМ',
             'default' => '0',
             'show' => '1',
             'sort' => '1',
         ]);*/
        $get = $GroupsModel->getAllPrograms();
        return json_encode($get);
    }

    public function postProgramsAjax()
    {
        $GroupsModel = new GroupsModel();
        if (empty($_POST['program']['id'])) {
            $result = $GroupsModel->createProgram($_POST['program']);
        } else {
            $result = $GroupsModel->editProgram($_POST['program']);
        }
        return json_encode($result);
    }

    public function deleteProgramsAjax($id)
    {
        $GroupsModel = new GroupsModel();
        $result = $GroupsModel->deleteProgram($id);
        return json_encode($result);
    }

    public function getClasses()
    {
        $this->View->parseTpl('settings/classes', false)->parseTpl('main')->output();
    }

    public function getClassesAjax()
    {
        $GroupsModel = new GroupsModel();
        /* $GroupsModel->createProgram([
             'name' => 'Олимпиадная математика',
             'shortname' => 'ОМ',
             'default' => '0',
             'show' => '1',
             'sort' => '1',
         ]);*/
        $get = $GroupsModel->getAllClasses();
        return json_encode($get);
    }

    public function postClassesAjax()
    {
        $GroupsModel = new GroupsModel();
        if (empty($_POST['class']['id'])) {
            $result = $GroupsModel->createClass($_POST['class']);
        } else {
            $result = $GroupsModel->editClass($_POST['class']);
        }
        return json_encode($result);
    }

    public function deleteClassesAjax($id)
    {
        $GroupsModel = new GroupsModel();
        $result = $GroupsModel->deleteClass($id);
        return json_encode($result);
    }

    public function getGroups()
    {
        $this->View->parseTpl('settings/groups', false)->parseTpl('main')->output();
    }

    public function getGroupsTest(){
        $GroupsModel = new GroupsModel();
        $groups = $GroupsModel->getGroups();
        print_r($groups);
    }

    public function getGroupsAjax()
    {
        $GroupsModel = new GroupsModel();
        $groups = $GroupsModel->getGroups();
        $groups_data = $GroupsModel->getAllGroupsyncAssoc();
        $programs = $GroupsModel->getAllPrograms();
        $classes = $GroupsModel->getAllClasses();

       /* $ZoomaccountsModel = new ZoomaccountsModel();
        $zoomaccounts = $ZoomaccountsModel->getAllAccounts();*/

        $ZoomModel = new ZoomModel();
        $zoomaccounts = $ZoomModel->getUsers()['users'];

        $colors = [
            [
                'name' => 'Без цвета',
                'id' => '0'
            ],
            [
                'name' => 'Зеленый',
                'id' => '1',
            ],
            [
                'name' => 'Синий',
                'id' => '2'
            ],
        ];

        $data = [
            'groups' => $groups,
            'groups_data' => $groups_data,
            'programs' => $programs,
            'classes' => $classes,
            'zoomaccounts' => $zoomaccounts,
            'colors' => $colors,
        ];
        return json_encode($data);
    }

    public function postGroupsAjax()
    {
        $GroupsModel = new GroupsModel();

        if($_POST['program']=='NULL')
            $_POST['program'] = NULL;

        if($_POST['class']=='NULL')
            $_POST['class'] = NULL;

        if($_POST['zoomaccount']=='NULL')
            $_POST['zoomaccount'] = NULL;

        if($_POST['manager_ids'])
            $_POST['manager_ids'] = json_encode($_POST['manager_ids']);
        else
            $_POST['manager_ids'] = NULL;

        $result = $GroupsModel->editGroupsync([
            'group_id_mk' => $_POST['id'],
            'program_id' => $_POST['program'],
            'class_id' => $_POST['class'],
            'zoomaccount_id' => $_POST['zoomaccount'],
            'comment' => $_POST['comment'],
            'color' => $_POST['color'],
            'individual' => $_POST['individual'],
            'show_adm' => $_POST['show_adm'],
            'show_user' => $_POST['show_user'],
            'begin_date' => $_POST['begin_date'],
            'manager_ids' => $_POST['manager_ids'],
        ]);
        return json_encode($_POST);
    }

    // Страница зум аккаунтов
    public function getZoomaccounts()
    {
        $this->View->parseTpl('settings/zoomaccounts', false)->parseTpl('main')->output();
    }

    public function getZoomaccountsListAjax()
    {
        $ZoomaccountsModel = new ZoomaccountsModel();
        die(json_encode($ZoomaccountsModel->getAllAccounts()));
    }

    // Добавление, редактирование
    public function postZoomaccounts()
    {
        $ZoomaccountsModel = new ZoomaccountsModel();
        $ZoomaccountsModel->editAccount($_POST['zoomaccounts']);
        print_r($_POST);
        return 'add,edit';
    }

    // Страница зум аккаунтов
    public function deleteZoomaccounts(int $id = 0)
    {
        $ZoomaccountsModel = new ZoomaccountsModel();
        $ZoomaccountsModel->delAccount($id);
        return 'del '.$id;
    }

    public function anyWhatsapp()
    {
        DB::init();


        if(!empty($_POST['whatsapp'])){
            DB::setOption('systemsetting', 'whatsapp_message', $_POST['whatsapp']['message']);
            DB::setOption('systemsetting', 'whatsapp_time', $_POST['whatsapp']['time']);
            DB::setOption('systemsetting', 'whatsapp_phone', $_POST['whatsapp']['phone']);
            DB::setOption('systemsetting', 'whatsapp_debug', $_POST['whatsapp']['debug']);
            $this->View->setVar('SAVE', 'true');
        }


        $this->View->setVars([
            'whatsapp_message' => DB::getOption('systemsetting', 'whatsapp_message'),
            'whatsapp_time' => DB::getOption('systemsetting', 'whatsapp_time'),
            'whatsapp_phone' => DB::getOption('systemsetting', 'whatsapp_phone'),
            'whatsapp_debug' => DB::getOption('systemsetting', 'whatsapp_debug')
        ]);
        $this->View->parseTpl('settings/whatsapp', false)->parseTpl('main')->output();
    }

    public function getHomeworkLinks(){
        $this->View->parseTpl('settings/homeworklinks', false)->parseTpl('main')->output();
    }

    public function getHomeworkLinksAjax(){
        $GroupsModel = new GroupsModel();
        $programs = $GroupsModel->getAllPrograms();
        $Homeworklinks = new HomeworklinksModel();
        $programs_data = $Homeworklinks->getHomeworklinksAllAssoc();
        $data = [
            'programs' => $programs,
            'programs_data' => $programs_data,

        ];
        return json_encode($data);
    }

    public function postHomeworkLinksAjax(){
        $Homeworklinks = new HomeworklinksModel();
        return $Homeworklinks->editHomeworklink([
            'id' => @$_POST['id'],
            'program_id' => @$_POST['program_id'],
            'name' => @$_POST['name'],
            'group' => @$_POST['group'],
            'link' => @$_POST['link'],
        ]);
    }

    public function postHomeworkLinksDeleteAjax(){
        $Homeworklinks = new HomeworklinksModel();
        return $Homeworklinks->deleteHomeworklink($_POST['id']);
    }

}