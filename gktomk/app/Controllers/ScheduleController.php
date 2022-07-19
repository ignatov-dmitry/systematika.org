<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\ScheduleModel;
use GKTOMK\Models\SubscriptionsModel;
use GKTOMK\Views\IndexView;

class ScheduleController extends Controller
{

    /**
     * @var IndexView
     */
    private $View;

    public function __construct()
    {
        $this->View = new IndexView();
    }

    public function main()
    {

        $this->View->varTpl('GET_EMAIL', @$_GET['email']);

        $email = 'test@gk.ru';

        if (isset($_GET['email']))
            $email = $_GET['email'];


        $Schedule = new ScheduleModel();
        $lessons = $Schedule->getSchedule($email);

        $SubscriptionsModel = new SubscriptionsModel();
        $countUserSubscriptions = $SubscriptionsModel->getCountSubscriptionsByEmail($email);


        $this->View->varTpl('LESSONS', $lessons);
        $this->View->varTpl('COUNT_SUBSCRIPTIONS_REMIND', [
            'individual' => $countUserSubscriptions['individual']['visitCount'] - $countUserSubscriptions['individual']['visitedCount'],
            'group' => $countUserSubscriptions['group']['visitCount'] - $countUserSubscriptions['group']['visitedCount'],
        ]);


        $this->View->parseTpl('schedule/index', false)->parseTpl('schedule/main')->output();
    }

    public function getEventsJson()
    {

        $email = 'test@gk.ru';

        if (isset($_GET['email']) and !empty($_GET['email']))
            $email = $_GET['email'];


        //echo $_GET['email'];

        $Schedule = new ScheduleModel();
        $lessons = $Schedule->getSchedule($email);

        //print_r($lessons);
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
}