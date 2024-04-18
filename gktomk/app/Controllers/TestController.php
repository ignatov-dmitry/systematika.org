<?php


namespace GKTOMK\Controllers;


use DateTime;
use GKTOMK\Classes\Api\MoyKlass;
use GKTOMK\Models\DB;
use GKTOMK\Models\EventsMoyklass;
use GKTOMK\Models\GetCourse\Account;
use GKTOMK\Models\GetcourseModel;
use GKTOMK\Models\LessonsModel;
use GKTOMK\Models\MemberModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\Systematika\MoyKlass\Lesson;
use GKTOMK\Models\Systematika\MoyKlass\LessonRecord;
use GKTOMK\Models\Systematika\MoyKlass\User;
use GKTOMK\Models\Systematika\MoyKlass\UserSubscription;
use GKTOMK\Models\VideorecordsModel;
use GKTOMK\Models\Wazzup24Model;
use GKTOMK\Models\WhatsappModel;
use GKTOMK\Models\ZoomModel;

class TestController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDate()
    {
        var_dump((new DateTime()));
        var_dump(date_diff(new \DateTime(), new \DateTime('2023-07-06 00:00'))->h);
    }

    public function getMessage()
    {
        $currentDateTime = new DateTime();
        $lessonDateTime = new DateTime('2024-03-04 19:50');

        $lessonRecords = new LessonRecord();
        //var_dump($lessonRecords->getRecordsWithUsers(30162904, '2024-03-04'));die();
        (new WhatsappModel())->sendMessages($lessonRecords->getRecordsWithUsers(30162904, '2024-03-04'), ['object' => ['beginTime' => '19:50', 'topic' => 'https://us06web.zoom.us/j/88978107600']]);
        //(new WhatsappModel())->sendApiWazzup('79858683061', 'wqeqwewqeqweqwe', 'sdasdasdasd');
    }

    public function getCall()
    {
        $MK = new MoyKlass();

        //Get all classes
        $MK->insertApiDataToDB('getClasses', 'mk_classes', false);

        //Get all courses
        $MK->insertApiDataToDB('getCourses', 'mk_courses', false);

        //Get all lesson records
        $MK->insertApiDataToDB('getLessonRecords', 'mk_lesson_records', true, 'lessonRecords', 'company/lessonRecords');

        //Get all lessons
        $MK->insertApiDataToDB('getLessons', 'mk_lessons', true, 'lessons', 'company/lessons',);

        //Get subscriptions
        $MK->insertApiDataToDB('getSubscriptions', 'mk_subscriptions', true, 'subscriptions', 'company/subscriptions',);

        //Get user subscriptions
        $MK->insertApiDataToDB('getUserSubscriptions', 'mk_user_subscriptions', true, 'subscriptions', 'company/userSubscriptions',);

        //Get all users
        $MK->insertApiDataToDB('getUsers', 'mk_users', true, 'users', 'company/users',);
    }

    public function getCourses()
    {
        $items = [];
        $keys = [];
        $tableName = 'mk_lesson_records';
        $MK = new MoyKlass();

        $filterData = [
            'date[0]'   => date('Y-m-d'),
            'date[1]'   => date('Y-m-d', strtotime(date('Y-m-d') . '+2 years')),
            'sort'      => 'id',
            'limit'     => 500,
            'offset'    => 0
        ];
        $userSubscriptions = $MK->getLessonRecords($filterData);

        foreach (Model::getColumnValues($userSubscriptions['lessonRecords'], Model::getInstance()->getTableColumn($tableName)) as $item) {
            $keys = array_keys($item);
            $items[] = $item;
        }

        $sql = Model::getInstance()->prepareBulkInsert($tableName, $keys, $items, true);

        //DB::exec($sql);
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
        foreach ($lesson->getLessonsWithRecordsByAllUsers() as $item) {
            $subscriptions = array();
            $userId = (new User())->getItem(['email' => $item['email']], ['id'])['id'];
            $userSubscriptions = (new UserSubscription())->getUserSubscriptionsFromId($userId);

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

    public function getVideo()
    {
        $videoRecords = new VideorecordsModel();
        $videoRecords->cronAddtasks();

        $records = $videoRecords->getReadyRecordsForDownloads();
        //var_dump($records);die();

        foreach ($records as $record) {
            $res = $videoRecords->downloadRecordById($record['id']);
            //var_dump($res);
        }
    }

    public function getUsersVideo()
    {
        $zoomModel = new ZoomModel();
        $zoomUsers = $zoomModel->getUsers(['status' => 'active', 'page_size' => 300])['users'];
        $meetings = array();


        foreach ($zoomUsers as $zoomUser) {
            $meeting = $zoomModel->getRecordings($zoomUser['id'],
                [
                    'from' => '2022-01-01',
                    'to' => date('Y-m-d', strtotime(date('Y-m-d') . '-1 day')),
                    'page_size' => 300
                ])['meetings'];

            $meetings = array_merge($meetings, $meeting);
        }
        return $zoomModel->createZoomMeetings($meetings);
    }

    public function getDownloadUsersVideo()
    {
        $zoomModel = new ZoomModel();

        $criteria = [
            array('key' => 'zmr.download_status', 'val' => 'not_started', 'op' => Model::OP_EQUAL),
            array('key' => 'zmr.zoom_status', 'val' => 'completed', 'op' => Model::OP_EQUAL),
            'file_extension' => 'MP4'
        ];

        foreach ($zoomModel->getZoomMeetings($criteria, 'recording_start ASC, meeting_id ASC', 2) as $zoomVideo) {
            $time = strtotime($zoomVideo['recording_start']);
            $Y = date("Y", $time);
            $m = date("m", $time);
            $d = date("d", $time);
            $dir = $Y . '/' . $m . '/' . $d;

            $zoomModel->setStatusRecordById($zoomVideo['id'], 'start_download');
            $zoomModel->setDataRecord($zoomVideo['id'], 'try_num', (int)++$zoomVideo['try_num']);
            $zoomModel->setDataRecord($zoomVideo['id'], 'try_date', date('Y-m-d H:i:s'));

            $downloadUrl = $zoomModel->getLinkDownloadByUrl($zoomVideo['download_url']);

            $status = $zoomModel->downloadByLink($downloadUrl, 'videorecord/unassigned_videos/' . $dir, $zoomVideo['topic'] . '_' . $zoomVideo['recording_type'], $zoomVideo['file_extension']);

            $zoomModel->setStatusRecordById($zoomVideo['id'], $status);

            if ($status === 'downloaded')
                $zoomModel->setDataRecord($zoomVideo['id'], 'file_name', 'videorecord/unassigned_videos/' . $dir . '/' . $zoomVideo['topic'] . '_' . $zoomVideo['recording_type']);

            if ($zoomModel->getCountVideosFromMeeting($zoomVideo['meeting_id']) === 0)
                $zoomModel->deleteMeeting(urlencode(urlencode($zoomVideo['meeting_id'])));

        }
    }

    public function getCronTest()
    {
        $WhatsappModel = new WhatsappModel();
        $WhatsappModel->cronStart();
    }

    public function getLessonRecords()
    {
        $lessonRecords = new LessonRecord();

        (new WhatsappModel())->sendMessages($lessonRecords->getRecordsWithUsers(25776466));
    }

    public function getGkExport()
    {
//        $gkUser = new \GKTOMK\Models\GetCourse\User();
//        $gkUser::setAccountName(CONFIG['gk_account_name']);
//        // Замените токен на сгенерированный вашим аккаунтом (http://{your_account}.getcourse.ru/saas/account/api)
//        $gkUser::setAccessToken(CONFIG['gk_secret_key']);
//        var_dump($gkUser->apiCall(''));

        $account = new Account();
        $account::setAccountName(CONFIG['gk_account_name']);
        $account::setAccessToken(CONFIG['gk_secret_key']);
        var_dump($account->apiCall('users', ['status' => 'active']));
    }

    public function getCounts()
    {
//        $userSubscription = new UserSubscription();
//        $userSubscription->prepareForGK('89104170140@mail.ru');

        $sql = "
            SELECT TIMESTAMPDIFF(HOUR, mu.last_update, CURRENT_TIMESTAMP()) as hour, mu.id, email FROM mk_users as mu
            LEFT JOIN mk_user_subscriptions as mus ON mu.id = mus.userId
            WHERE mus.statusId = 2 AND email is not null
            GROUP BY email
            HAVING hour > 6 OR hour IS null
            LIMIT 30;
        ";

        $data = DB::getAll($sql);

        foreach ($data as $item)
        {
            //$userMk = MoyklassModel::getUserById(['userId' => $item['id']]);
            $GetCourse = new GetcourseModel();
            $GetCourse->updateUserDateVisitByUserIdMK($item['id'])
                ->updateUserSubscriptionsByUserIdMK($item['id'])
                ->setEmail($item['email'])
                ->sendUser();
            DB::exec("UPDATE mk_users set last_update = CURRENT_TIMESTAMP() where email = '" . $item['email'] . "'");
        }
    }

    public function getTest()
    {

//        $MK = new MoyKlass();
//        $data = $MK->getAllDataFromApi('getUserSubscriptions', 'mk_user_subscriptions', 'subscriptions', ['statusId' => 2], 'company/userSubscriptions');
//
//        $sql = "DELETE FROM mk_user_subscriptions WHERE id in (" . implode(',', array_column($data, 'id')) . ")";
//        DB::exec($sql);
//
//        $sql = Model::getInstance()->prepareBulkInsert('mk_user_subscriptions', array_keys($data[0]), $data);
//        DB::exec($sql);


        $userMk = MoyklassModel::getUserByIdFromDb(4439931);
        $GetCourse = new GetcourseModel();
        $GetCourse->updateUserDateVisitByUserIdMK(4439931)
            ->updateUserSubscriptionsByUserIdMK(4439931)
            ->setEmail($userMk['email'])
            ->sendUser();

        //$userMk = MoyklassModel::getUserById(4439931);
    }
}