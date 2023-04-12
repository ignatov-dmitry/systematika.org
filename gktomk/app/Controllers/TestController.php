<?php


namespace GKTOMK\Controllers;


use GKTOMK\Classes\Api\MoyKlass;
use GKTOMK\Models\Systematika\MoyKlass\Lesson;
use GKTOMK\Models\Systematika\MoyKlass\User;
use GKTOMK\Models\Systematika\MoyKlass\UserSubscription;
use GKTOMK\Models\VideorecordsModel;
use GKTOMK\Models\Wazzup24Model;
use GKTOMK\Models\ZoomModel;

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
        $zoomModel = new ZoomModel('pfTr1IlDS6qnpHWAq5TR7A', 'W000GLZCo2LZPbPrQB5Soec1SxCleZXl39RN');
        $zoomUsers = $zoomModel->getUsers(['status' => 'active', 'page_size' => 300])['users'];
        $meetings = array();


        foreach ($zoomUsers as $zoomUser) {
            $meeting = $zoomModel->getRecordings($zoomUser['id'],
                [
                    'from' => '2022-01-01',
                    'to' => date('Y-m-d'),
                    'page_size' => 300
                ])['meetings'];

            $meetings = array_merge($meetings, $meeting);
        }
        return $zoomModel->createZoomMeetings($meetings);
    }

    public function getDownloadUsersVideo()
    {
        $zoomModel = new ZoomModel('pfTr1IlDS6qnpHWAq5TR7A', 'W000GLZCo2LZPbPrQB5Soec1SxCleZXl39RN');

        foreach ($zoomModel->getZoomMeetings() as $zoomVideo) {
            $time = strtotime($zoomVideo['recording_start']);
            $Y = date("Y", $time);
            $m = date("m", $time);
            $d = date("d", $time);
            $dir = $Y . '/' . $m . '/' . $d;

            $zoomModel->setStatusRecordById($zoomVideo['id'], 'start_download');
            $zoomModel->setDataRecord($zoomVideo['id'], 'try_num', (int)$zoomVideo['try_num']++);
            $zoomModel->setDataRecord($zoomVideo['id'], 'try_date', date('Y-m-d H:i:s'));

            $downloadUrl = $zoomModel->getLinkDownloadByUrl($zoomVideo['download_url']);
            var_dump($zoomVideo['download_url']);
            $status = $zoomModel->downloadByLink($downloadUrl, 'videorecord/unassigned_videos/' . $dir, $zoomVideo['topic'] . '_' . $zoomVideo['recording_type'], $zoomVideo['file_extension']);

            $zoomModel->setStatusRecordById($zoomVideo['id'], $status);

            if ($status === 'downloaded')
                $zoomModel->setDataRecord($zoomVideo['id'], 'file_name', 'videorecord/unassigned_videos/' . $dir . '/' . $zoomVideo['topic'] . '_' . $zoomVideo['recording_type']);

            if ($zoomModel->getCountVideosFromMeeting($zoomVideo['meeting_id']) === 0)
                $zoomModel->deleteMeeting(urlencode(urlencode($zoomVideo['meeting_id'])));

        }
    }
}