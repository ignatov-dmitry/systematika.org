<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\LogsModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\ScheduleModel;
use GKTOMK\Models\SubscriptionsModel;
use GKTOMK\Models\UserdatevisitModel;

class WidgetController extends Controller
{
    public $View = '';

    public function __construct()
    {
        parent::__construct();
        $this->View = new \GKTOMK\Views\IndexView();

    }

    public function main($email = 'string')
    {
        header('Access-Control-Allow-Origin: *');
        $users = MoyklassModel::getFindUsers(['email' => $email]);
        if (empty($users['users'][0]['id']) or $users['users'][0]['id'] < 1) {
            $this->answerAjax(['userId' => 0]);
        } else {
            $this->answerAjax(['userId' => $users['users'][0]['id']]);
        }
    }

    public function getGo($email = 'string')
    {

        $users = MoyklassModel::getFindUsers(['email' => $email]);
        if (empty($users['users'][0]['id']) or $users['users'][0]['id'] < 1) {
            $this->View->parseTpl('redirect', false)->parseTpl('main')->output();
            return;
        } else {
            $url = 'https://app.moyklass.com/user/' . $users['users'][0]['id'] . '/joins';
            header("Location: " . $url);
        }
        die();
    }

    public function getUserinfo($email = 'string')
    {

        $member = $this->Member->getMemberByEmail($email);


        if($member == null){
            $this->Member->is_not_found($email);
            return json_encode(['status' => 'error', 'text' => 'Пользователь не найден в интеграции, отправлен запрос на создание.']);
        }

        $mk_uid = $this->Member->getMemberParamMkUid($member['id']);

        if($mk_uid == null){
            return json_encode(['status' => 'error', 'text' => 'Пользователь не найден в MoyKlass.']);
        }

        $SubscriptionModel = new SubscriptionsModel();
        $getCountSubscriptionsByEmail = $SubscriptionModel->getCountSubscriptionsByMkUid($mk_uid);

        $getUserDateVisit = UserdatevisitModel::getUserDateVisitByMkUid($mk_uid);


        $getUserDateVisit['date_last_lesson'] = $this->timeFormatUserinfo(strtotime($getUserDateVisit['date_last_lesson']));

        return json_encode([
            'subscription' => $getCountSubscriptionsByEmail,
            'datevisit' => $getUserDateVisit
        ]);

    }

    /*
     * Виджет в ГК (табличка с записями)
     * */
    public function getUserrecords($email = 'string')
    {
        $member = $this->Member->getMemberByEmail($email);

        if($member == null){
            $this->Member->is_not_found($email);
            return json_encode(['status' => 'error', 'text' => 'Пользователь не найден в интеграции, отправлен запрос на создание.']);
        }


        $mk_uid = $this->Member->getMemberParamMkUid($member['id']);

        if($mk_uid == null){
            return json_encode(['status' => 'error', 'text' => 'Пользователь не найден в MoyKlass.']);
        }


        $Schedule = new ScheduleModel();
        $lessons = $Schedule->getScheduleWidget($member['id'], $mk_uid);

        if(empty($lessons))
            $lessons = [];


        return json_encode($lessons);
        //print_r($lessons);
    }

    public function getTest($email = 'string'){
        $member = $this->Member->getMemberByEmail($email);

        var_dump($member);

        if($member == null){
            $res = $this->Member->is_not_found($email);
            return json_encode(['status' => 'error', 'text' => 'Пользователь не найден в интеграции, отправлен запрос на создание.']);
        }
    }

    private function timeFormatUserinfo($time){
            date_default_timezone_set('Europe/Moscow');
            $ndate = date('d.m.Y', $time);
            $ndate_time = date('H:i', $time);
            $ndate_exp = explode('.', $ndate);
            $nmonth = array(
                1 => 'янв',
                2 => 'фев',
                3 => 'мар',
                4 => 'апр',
                5 => 'мая',
                6 => 'июн',
                7 => 'июл',
                8 => 'авг',
                9 => 'сен',
                10 => 'окт',
                11 => 'ноя',
                12 => 'дек'
            );

            foreach ($nmonth as $key => $value) {
                if($key == intval($ndate_exp[1])) $nmonth_name = $value;
            }

            if($ndate == date('d.m.Y')) return 'сегодня';
            elseif($ndate == date('d.m.Y', strtotime('-1 day'))) return 'вчера';
            elseif($ndate == date('d.m.Y', strtotime('-2 day'))) return 'позавчера';
            else{

                $result = $ndate_exp[0].' '.$nmonth_name;

                if($ndate_exp[2] < date('Y')){
                    $result .= ' '.$ndate_exp[2];
                }


                return $result;
            }

    }

}