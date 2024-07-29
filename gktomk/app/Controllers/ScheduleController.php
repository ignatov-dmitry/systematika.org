<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\CancelLessonModel;
use GKTOMK\Models\LessonsModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\ScheduleactivitylogModel;
use GKTOMK\Models\ScheduleModel;
use GKTOMK\Models\SubscriptionsModel;

class ScheduleController extends Controller
{
    private $email;
    private $userData;
    private $mk_uid;

    public function __construct()
    {

        parent::__construct();
        $this->Member->is_auth();

        // Правило для всех клиентов
        if(!$this->Member->isAccess('1'))
            $_SESSION['email'] = $this->Member->getMemberData()['email'];

        // Отдельные правила для админов
        if($this->Member->isAccess('1')){

            if(empty($_SESSION['email']) or $_GET['email']=='my')
                $_SESSION['email'] = $this->Member->getMemberData()['email'];

            if(!empty($_GET['email']) and $_GET['email']!=='my' and $_GET['email']!==$_SESSION['email']){
                $_SESSION['email'] = $_GET['email'];
            }
        }


        // Пользователь не найден
        if(!$this->Member->is_not_found($_SESSION['email'])){
            die('Доступ к расписанию будет  доступен в течение 5-10 минут.<br/>Зайдите позже.');
        }


        $this->email = $_SESSION['email'];
        $this->userData = $this->Member->getMemberByEmail($this->email);



        /*if(empty($this->userData['mk_uid'])){

        }*/

        $this->View->varTpl('GET_EMAIL', @$this->email);
        $this->View->varTpl('MEMBER', $this->userData);


        $this->mk_uid = $this->Member->getMemberParamMkUid(
            $this->userData['id']
        );

        //заглушка для пользователя kurmaeva_marina@mail.ru нужно удалить после редактирования в базе систематики
        if ('kurmaeva_marina@mail.ru' == mb_strtolower(@$this->email))
            $this->mk_uid = 1242164;

        if ('asanov@pintabeer.ru' == mb_strtolower(@$this->email))
            $this->mk_uid = 2287371;

        if ('ilal7@mail.ru' == mb_strtolower(@$this->email))
            $this->mk_uid = 3564822;

        if ('ftb@stencom.ru' == mb_strtolower(@$this->email))
            $this->mk_uid = 5208185;

        if ('julietta.tm@gmail.com' == mb_strtolower(@$this->email))
            $this->mk_uid = 4520450;

    }

    public function main()
    {
        $lessons = [];
        $Schedule = new ScheduleModel();
        if ($this->mk_uid)
            $lessons = $Schedule->getSchedule($this->userData['id'], $this->mk_uid);
        //print_r($lessons);
        //echo 'Gen time schedule: '.$this->genTime('schedule');

        $SubscriptionsModel = new SubscriptionsModel();
        $countUserSubscriptions = $SubscriptionsModel->getCountSubscriptionsByMkUid($this->mk_uid);


        $this->View->varTpl('LESSONS', $lessons);
        $this->View->varTpl('TEACHERS', MoyklassModel::getManagersAssoc());
        //print_r($this->View->varTpl('TEACHERS'));
        $this->View->varTpl('COUNT_SUBSCRIPTIONS_REMIND', [
            'individual' => (int)@$countUserSubscriptions['individual']['visitCount'] - (int)@$countUserSubscriptions['individual']['visitedCount'],
            'group' => $countUserSubscriptions['group']['visitCount'] - $countUserSubscriptions['group']['visitedCount'],
        ]);


        $this->View->parseTpl('schedule/index', false)->parseTpl('schedule/main')->output();
    }

    public function getHistory()
    {
        $SubscriptionsModel = new SubscriptionsModel();
        $countUserSubscriptions = $SubscriptionsModel->getCountSubscriptionsByMkUid($this->mk_uid);
        $this->View->varTpl('COUNT_SUBSCRIPTIONS_REMIND', [
            'individual' => $countUserSubscriptions['individual']['visitCount'] - $countUserSubscriptions['individual']['visitedCount'],
            'group' => $countUserSubscriptions['group']['visitCount'] - $countUserSubscriptions['group']['visitedCount'],
        ]);

        $lessonsmodel = new LessonsModel();
        $lessons = $lessonsmodel->getLessonsByUserIdMk($this->mk_uid, $this->userData['id']);
        if($this->Member->isAccess(1) AND !empty($_GET['debug']))
            print_r($lessons);
        //$lessonsmodel->loadLessonsUserByUserIdMK($this->mk_uid);
        //print_r($lessons);


        $this->View->varTpl('LESSONS', $lessons);
        $this->View->varTpl('TEACHERS', MoyklassModel::getManagersAssoc());

        $this->View->parseTpl('schedule/history', false)->parseTpl('schedule/main')->output();
    }

    public function getEventsJson()
    {

        $Schedule = new ScheduleModel();
        $lessons = $Schedule->getSchedule($this->email);

        $data = [];
        foreach ($lessons as $lesson) {
            $name = $lesson['COURSE']['name'] . ' ';
            $name .= $lesson['CLASS']['name'];

            $data[] = [
                "id" => $lesson['id'],
                "name" => $name,
                "desc" => $lesson['topic'],
                "startdate" => $lesson['date'],
                "enddate" => '',
                "starttime" => $lesson['beginTime'],
                "endtime" => $lesson['endTime'],
                "color" => $lesson['CLASS']['color'],
                "url" => $lesson['url']
            ];


        }

        $jsonData['monthly'] = $data;

        die(json_encode($jsonData));

    }


    /*
     * Отмена занятия ajax-lesson-cancel
     * */
    public function postAjaxCancelLesson()
    {

        $CancelLessonModel = new CancelLessonModel();
        $cancelId = $CancelLessonModel->addCancel($this->userData['id'], $this->mk_uid, $_POST['lessonId']);
        $ScheduleactivitylogModel = new ScheduleactivitylogModel();
        $ScheduleactivitylogModel->addAction(
            'cancellesson',
            $this->Member->getMemberId(),
            $this->userData['id'],
            $_POST['lessonId'],
            [
                'cancelid' => $cancelId,
            ]
        );
        return json_encode($CancelLessonModel->sendCancel($cancelId));
    }

    public function getAjaxCancelLimit()
    {
        $CancelLessonModel = new CancelLessonModel();
        if (empty($_GET['lesson_date']))
            $_GET['lesson_date'] = time();

        //print_r($_GET);

        $result = $CancelLessonModel->countCancelLimit($this->userData['id'], $_GET['lesson_date'], $_GET['class_id']);
        return json_encode(['count_limit' => $result, 'date_month' => date("n", $_GET['lesson_date'])]);
    }

    public function getTest()
    {
        $SubscriptionsModel = new SubscriptionsModel();
        $countUserSubscriptions = $SubscriptionsModel->getCountSubscriptionsByMkUid(1720727);
        var_dump($countUserSubscriptions);
    }

    public function getAjaxLoadHistory()
    {
        //$lessonsmodel = new LessonsModel();
        //$lessonsmodel->loadLessonsUserByUserIdMK($this->mk_uid);
    }

    public function getAjaxActivity()
    {
        $ScheduleactivitylogModel = new ScheduleactivitylogModel();
        if ($this->Member->isAccess(1) and !empty($_GET['member_id']))
            $member_id = $_GET['member_id'];
        else
            $member_id = $this->Member->getMemberId();
        $result['result'] = $ScheduleactivitylogModel->getActionByActionsMemberIdAndObjectId($member_id, [$_GET['lesson_id'], $_GET['class_id']]);
        if ($this->Member->isAccess(1))
            $result['admin'] = 1;
        return json_encode($result);
    }

}
