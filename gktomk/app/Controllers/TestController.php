<?php


namespace GKTOMK\Controllers;


use GKTOMK\Classes\Api\MoyKlass;
use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\Systematika\MoyKlass\Lesson;
use GKTOMK\Models\Systematika\MoyKlass\User;
use GKTOMK\Models\Systematika\MoyKlass\UserSubscription;
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

    public function getCall()
    {
        $MK = new MoyKlass();

        //Get all classes
        $MK->insertApiDataToDB('getClasses', 'mk_classes', false);

        //Get all courses
        $MK->insertApiDataToDB('getCourses', 'mk_courses', false);

        //Get all lesson records
        $MK->insertApiDataToDB('getLessonRecords', 'mk_lesson_records', true,  'lessonRecords', 'company/lessonRecords');

        //Get all lessons
        $MK->insertApiDataToDB('getLessons', 'mk_lessons', true, 'lessons','company/lessons',);

        //Get subscriptions
        $MK->insertApiDataToDB('getSubscriptions', 'mk_subscriptions', true, 'subscriptions', 'company/subscriptions',);

        //Get user subscriptions
        $MK->insertApiDataToDB('getUserSubscriptions', 'mk_user_subscriptions', true, 'subscriptions','company/userSubscriptions',);

        //Get all users
        $MK->insertApiDataToDB('getUsers', 'mk_users', true, 'users', 'company/users',);
    }

    public function getUpdateUsers()
    {
        $listCsv = array();
        $lesson = new Lesson();

        $listCsv[] = array(
            'Email',
            'Дата последнего пробного занятия',
            'Дата последнего посещения занятия',
            'Дата пропуска последнего урока',
            'Ближайшая платная запись',
            'Ближайшая бесплатная запись',
            'Количество абонементов',
            'Оставшееся количество посещений',
            'Остаток индивидуальных занятий',
            'Остаток групповых занятий'
        );


        $items = array();
        foreach ($lesson->getLessonsWithRecordsByAllUsers() as $item)
        {
            $subscriptions = array();
            $userId = (new User())->getItem(['email' => $item['email']], ['id'])['id'];
            $userSubscriptions = (new UserSubscription())->getUserSubscriptions($userId);

            $subscriptions['count_user_subscriptions'] = $userSubscriptions['all']['itemCount'] ?: '999';
            $subscriptions['user_subscriptions_left_visits'] = ($userSubscriptions['all']['visitCount'] - $userSubscriptions['all']['visitedCount']) ?: '999';
            $subscriptions['user_subscriptions_left_visits_individual'] = ($userSubscriptions['individual']['visitCount'] - $userSubscriptions['individual']['visitedCount']) ?: '999';
            $subscriptions['user_subscriptions_left_visits_group'] = ($userSubscriptions['group']['visitCount'] - $userSubscriptions['group']['visitedCount']) ?: '999';

            $items[] = array_merge($item, array_values($subscriptions));
        }

        $listCsv = array_merge($listCsv, $items);

        $fp = fopen('users-update.csv', 'w');

        foreach ($listCsv as $fields) {
            fputcsv($fp, $fields, ';');
        }
        fclose($fp);
    }
}