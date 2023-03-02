<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\Systematika\MoyKlass;
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
}