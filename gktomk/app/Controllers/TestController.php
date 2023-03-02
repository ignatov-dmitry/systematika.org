<?php


namespace GKTOMK\Controllers;



use GKTOMK\Models\DB;
use GKTOMK\Models\GetCourse\Export;
use GKTOMK\Models\GetcourseModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\Systematika\MoyKlass;
use GKTOMK\Models\Wazzup24Model;

class TestController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getExport()
    {
        $export = new Export();
        $export::setAccountName(CONFIG['gk_account_name']);
        $export::setAccessToken(CONFIG['gk_secret_key']);
        var_dump($export->apiCall('users'));
    }

    public function getMessage(){
        $wazzup = new Wazzup24Model();
        $wazzup->sendMessage();
    }

    public function getUsers()
    {
        $offset = 0;
        $total = 0;
        $users = array();
        $limit = 500;
        $getCourseUsers = array();

        do{
            $buf = MoyklassModel::getFindUsers(['limit' => $limit, 'offset' => $offset]);
            if ($total == 0)
                $total = $buf['stats']['totalItems'];
            $users = array_merge($users, $buf['users']);
            $offset += $limit;
        }
        while($total > $offset);

        foreach ($users as $user){
            foreach ($user['attributes'] as $attribute) {
                if ($attribute['attributeId'] == 2236){
                    //$getCourseUsers[] = $user['id'];
                    $GetCourse = new GetcourseModel();
                    $userMk = MoyklassModel::getUserByEmail($user['email']);
                    try {
                        $GetCourse->updateUserSubscriptions($user['email'], $userMk)
                            ->updateUserDateVisit($user['email'], $userMk)
                            ->sendUser();
                        echo 'OK ' . $user['email'] . '<br>';
                    }
                    catch (\Exception $exception){
                        echo 'Error ' . $user['email'] . '<br>';
                    }
                    break;
                }
            }
        }
        //var_dump($getCourseUsers);
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