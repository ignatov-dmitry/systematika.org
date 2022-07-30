<?php

namespace GKTOMK\Controllers;

use GKTOMK\Models\AddgroupModel;
use GKTOMK\Models\GroupsModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\ScheduleactivitylogModel;
use GKTOMK\Models\ScheduleModel;
use GKTOMK\Models\TelegramModel;


class AddgroupController extends Controller
{
    private $userId;
    private $userData;
    private $email;

    public function __construct()
    {
        parent::__construct();
        $this->Member->is_auth();

        if(!empty($_GET['email']) and $this->Member->is_not_found($_GET['email']) and $this->Member->isAccess('1')){
            $_SESSION['email'] = $_GET['email'];
            $this->Member->isAccess('1', true); // Доступ только для администраторов
        }else if($_GET['email']=='my')
            unset($_SESSION['email']);


        if(!empty($_SESSION['email']))
            $this->email = $_SESSION['email'];
        else{
            $this->email = $this->Member->getMemberData()['email'];
        }

        $this->userData = $this->Member->getMemberByEmail($this->email);

    }

    public function main()
    {
        if(empty($this->email)){
            return json_encode(['addgroup' => ['message' => 'Не указан email']]);
        }

        $this->userData = $this->Member->getMemberByEmail($this->email);

        if(empty($this->userData['id'])){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в системе.']]);
        }

        $this->userId = $this->Member->getMemberParamMkUid($this->userData['id']);
        if(empty($this->userId)){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в MoyKlass.']]);
        }

        $this->View->parseTpl('addgroup/addgroup', false)->parseTpl('addgroup/main')->output();
    }


    public function getDataAjax(){
        $GroupsModel = new GroupsModel();
        $groups_data = $GroupsModel->getAllGroupsync();
        $programs = $GroupsModel->getAllPrograms();
        $classes = $GroupsModel->getAllClassesAssoc();
        $managers = MoyklassModel::getManagers();
        $colors = [
            [
                'name' => 'Зеленый',
                'id' => '1',
            ],
            [
                'name' => 'Синий',
                'id' => '2'
            ]
        ];

        $data = [
            /*'groups' => $groups,*/
            'groups_data' => $groups_data,
            'programs' => $programs,
            'classes' => $classes,
            'managers' => $managers,
            'colors' => $colors,
        ];
        return json_encode($data);
    }

    public function getDataGroupAjax($idGroupMK=0){

        if(empty($this->email)){
            return json_encode(['addgroup' => ['message' => 'Не указан email']]);
        }

        $member = $this->Member->getMemberByEmail($this->email);
        if(empty($member['id'])){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в системе.']]);
        }

        $userId = $this->Member->getMemberParamMkUid($member['id']);
        if(empty($userId)){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в MoyKlass.']]);
        }


        $group = MoyklassModel::getClassByIdMK($idGroupMK);

        $GroupsModel = new GroupsModel();
        $getGroupSync = $GroupsModel->getGroupsyncByGroupIdMK($idGroupMK);
        $getProgram = $GroupsModel->getProgramById($getGroupSync['program_id']);
        $getClass = $GroupsModel->getClassById($getGroupSync['class_id']);

        $group['programname'] = $getProgram[0]['name'];
        $group['classname'] = $getClass[0]['name'];
        if (strpos($group['classname'], 'класс') == false) {
            $group['classname'] = 'класс ' . $group['classname'];
        }

        $weekdays = array("1" => "понедельник", "2" => "вторник", "3" => "среда", "4" => "четверг", "5" => "пятница", "6" => "суббота", "7" => "воскресенье");
        $group['weekdaytime'] = $weekdays[date("N", strtotime($group['beginDate']))] . ' ' .date("H:i", strtotime($group['beginDate']));



        $ScheduleModel = new ScheduleModel();
        $schedule = $ScheduleModel->getScheduleWidget($member['id'], $userId, $idGroupMK);

        $teachers = [];
        foreach ($group['managerIds'] as $managerId) {
            $teacher = MoyklassModel::getManagerById($managerId);
            $teachers[] = $teacher['name'];
        }

        $month_now_first_day = date('Y-m-d', time());
        $month_now_last_day = date('Y-m-d', (time() + (60 * 60 * 24 * 15)));
        $lessons = MoyklassModel::getLessons(['classId' => $idGroupMK, 'date[0]' => $month_now_first_day,
            'date[1]' => $month_now_last_day]);



        $resultLessons = [];
        $i = 0;
        foreach ($lessons['lessons'] as $lesson){
            $lesson['timestart'] = strtotime($lesson['date'] . ' ' . $lesson['beginTime']);
            if($i>=2)
                break;

            if($lesson['timestart'] < time())
                continue;

            $resultLessons[] = $lesson;
            $i++;
        }

        
        $result = [
            'groupsync' => $getGroupSync,
            'group' => $group,
            'schedule' => $schedule,
            'teachers' => $teachers,
            'lessons' => $resultLessons
        ];

        return json_encode($result);
    }

    public function postAddgroupAjax(){

        $email = $_POST['email'];

        if(empty($email)){
            return json_encode(['addgroup' => ['message' => 'Не указан email']]);
        }

        $member = $this->Member->getMemberByEmail($email);
        if(empty($member['id'])){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в системе.']]);
        }

        $userId = $this->Member->getMemberParamMkUid($member['id']);
        if(empty($userId)){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в MoyKlass.']]);
        }
        $classId = $_POST['idgroup'];
        if(empty($classId)){
            return json_encode(['addgroup' => ['message' => 'Не указана группа.']]);
        }

        $AddgroupModel = new AddgroupModel();
        $ScheduleactivitylogModel = new ScheduleactivitylogModel(); // Для добавляния действия в лог
        //$result = $AddgroupModel->addJoinGroup($_POST);


        switch($_POST['periodLesson']){

            case 'onetime':

                $statusId = CONFIG['statusGroup']['recorded']; // Статус "Записан" - id 31034
                $autoJoin = false;
                $result['addgroup'] = $AddgroupModel->editJoinGroupByClassId($userId, $classId, $statusId, $autoJoin);

                if($_POST['dateLesson']=='nearest'){
                    $result['addlesson'] = $AddgroupModel->addRecordLesson($userId, $_POST['idLessonNearest']);
                }elseif($_POST['dateLesson']=='next'){
                    $result['addlesson'] = $AddgroupModel->addRecordLesson($userId, $_POST['idLessonNext']);
                }

                break;

            case 'always':

                $statusId = CONFIG['statusGroup']['learns']; // Статус "Учится" - id 2
                $autoJoin = true; // Автоматически записывать на все занятие в группе
                $result['addgroup'] = $AddgroupModel->editJoinGroupByClassId($userId, $classId, $statusId, $autoJoin);


                $excludeLessons = [];
                if($_POST['dateLesson']=='next'){ // Если записываем в группу со следующего занятия. То исключаем запись на ближайщее занятие
                    $excludeLessons[] = $_POST['idLessonNearest'];
                }
                $result['addlesson'] = $AddgroupModel->addRecordAllLessonByClassId($userId, $classId, $excludeLessons);



                break;
            default:
                return json_encode(['addgroup' => ['message' => 'Неверно указан период.']]);
                break;

        }

        # Записываем в лог добавление в группу
        $ScheduleactivitylogModel->addAction(
            'addgroup',
            $this->Member->getMemberId(),
            $member['id'],
            $classId,
            ['periodlesson' => $_POST['periodLesson'], 'datelesson' => $_POST['dateLesson'], 'result' => $result]
        );

        return json_encode($result);
    }

    /** Удаление архивных группу (не актуально)
     *
     * */
    public function getDeleteInactivGroups(){
        /*$GroupsModel = new GroupsModel();
        $res = $GroupsModel->deleteInactiveGroups();
        var_dump($res);*/
    }

    /*
     * Пользовательское добавление
     * */
    public function getUserAdd()
    {


        if(empty($this->email)){
            return json_encode(['addgroup' => ['message' => 'Не указан email']]);
        }

        $this->userData = $this->Member->getMemberByEmail($this->email);

        if(empty($this->userData['id'])){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в системе.']]);
        }

        $this->userId = $this->Member->getMemberParamMkUid($this->userData['id']);
        if(empty($this->userId)){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в MoyKlass.']]);
        }

        $this->View->parseTpl('addgroup/user-add', false)->parseTpl('addgroup/main')->output();

    }

    public function getDataGroupUserAjax($idGroupMK=0){
        $_GET['email'] = $this->email;
        return $this->getDataGroupAjax($idGroupMK);
    }

    public function postAddgroupUserAjax(){


        $this->userData = $this->Member->getMemberByEmail($this->email);

        if(empty($this->userData['id'])){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в системе.']]);
        }

        $this->userId = $this->Member->getMemberParamMkUid($this->userData['id']);
        if(empty($this->userId)){
            return json_encode(['addgroup' => ['message' => 'Указанный пользователь не найден в MoyKlass.']]);
        }

        $subscriptions = MoyklassModel::getUserSubscriptions(['userId' => $this->userId]);
        if(($subscriptions['stats']['totalVisits']-$subscriptions['stats']['totalVisited']) < 1){
            return json_encode(['addgroup' => ['message' => 'На балансе недостаточно средств.']]);
        }

        $_POST['email'] = $this->email;
        return $this->postAddgroupAjax();
    }

    public function getTest(){
        $Addgroup = new AddgroupModel();
        $res = $Addgroup->editJoinGroupByClassId(834428, 180505, 31034, true);
        print_r($res);
    }

    public function getIndividual(){
        $this->View->parseTpl('addgroup/user-individual', false)->parseTpl('addgroup/main')->output();
    }

    public function postIndividualAjax(){
        $get = @$_REQUEST;

        $text = 'Заявка на подбор преподавателя!' . PHP_EOL;
        foreach ($get as $key => $value) {
            $key= str_replace('_', ' ', $key);
            if(!empty($value))
                $text .= $key . ': ' . $value . PHP_EOL;
        }

        $text .= "Пользователь " . @$this->userData['first_name'] . " " . @$this->userData['last_name']  . PHP_EOL;

        $inline_keyboard = [
            ['text' => 'Перейти', 'url' => CONFIG['url_gk'] . "/user/control/user/update/id/" . @$this->userData['gk_uid']],
        ];

        return TelegramModel::sendMessage(CONFIG['support_group_id'], $text, $inline_keyboard);
      }

}