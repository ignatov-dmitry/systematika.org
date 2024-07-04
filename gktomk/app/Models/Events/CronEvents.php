<?php


namespace GKTOMK\Models\Events;


use GKTOMK\Classes\Api\MoyKlass;
use GKTOMK\Models\ChatModels\ChatAdminModel;
use GKTOMK\Models\DB;
use GKTOMK\Models\Events;
use GKTOMK\Models\GetcourseModel;
use GKTOMK\Models\HandlerHwkModel;
use GKTOMK\Models\LessonsModel;
use GKTOMK\Models\MissingTrialModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\StatisticsModel;
use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\VideorecordsModel;
use GKTOMK\Models\ZoomModel;

class CronEvents extends Events
{

    public $MK;

    public function __construct($request)
    {
        $this->MK = new MoyKlass();
        parent::__construct($request);
    }

    public function handle()
    {
        if (method_exists($this, $this->request['event'])) {
            return $this->{$this->request['event']}();
        }
    }

    private function visitslesson_everyday(){
        $stats = new StatisticsModel();

        $time = time();
        $period = $time - (60*60*24*7); // Обновляем на 7 дней назад
        $datestart = date("Y-m-d", $period);
        $dateend = date("Y-m-d", $time);

        $stats->getLoadVisits($datestart, $dateend);
    }

    private function visitslesson_everyhour(){
        $stats = new StatisticsModel();

        $time = time();
        $period = $time + (60*60*24*7); // Обновляем на 7 дней вперед
        $datestart = date("Y-m-d", $time);
        $dateend = date("Y-m-d", $period);

        $stats->getLoadVisits($datestart, $dateend);
    }

    // Обработка выдачи домашних заданий
    private function homeworks_every1minute(){
        $HandlerHwkModel = new HandlerHwkModel();
        $res = $HandlerHwkModel->cronHandle();
       // var_dump($res);
    }

    // Обработка пропусков
    private function missings_every1minute(){
        // Запускам обработку пропусков
        $MissingTrial = new MissingTrialModel();
        $MissingTrial->handleMissings();
    }

    // Обработка отмененных занятий
    private function cancellesson_every5minute(){

    }

    private function unassigned_videos()
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

    private function videorecords_every1minute(){
        $VideorecordsModel = new VideorecordsModel();
        $VideorecordsModel->cronAddtasks(); // Добавляем задачи в лог на сохранение видео (убрали из вебхуков)
        $VideorecordsModel->cronStart(); // Обрабатываем задачи

        $zoomModel = new ZoomModel();

        $criteria = [
            array('key' => 'zmr.download_status', 'val' => 'not_started', 'op' => Model::OP_EQUAL),
            array('key' => 'zmr.zoom_status', 'val' => 'completed', 'op' => Model::OP_EQUAL),
            'file_extension' => 'MP4'
        ];

        foreach ($zoomModel->getZoomMeetings($criteria, 'recording_start ASC, meeting_id ASC',2 ) as $zoomVideo) {
            $time = strtotime($zoomVideo['recording_start']);
            $Y = date("Y", $time);
            $m = date("m", $time);
            $d = date("d", $time);
            $dir = $Y . '/' . $m . '/' . $d;

            $zoomModel->setStatusRecordById($zoomVideo['id'], 'start_download');
            $zoomModel->setDataRecord($zoomVideo['id'], 'try_num', (int) ++$zoomVideo['try_num']);
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

    private function videorecords_lost(){

    }

    private function whatsapp_every1minute(){

    }

    private function synchronizationlessons_manual(){
        $LessonsModel = new LessonsModel();
        $LessonsModel->setSynchronizationByDate(time(), time());
    }

    public function update_classes()
    {
        $this->MK->insertApiDataToDB('getClasses', 'mk_classes', false);
    }

    public function update_courses()
    {
        $this->MK->insertApiDataToDB('getCourses', 'mk_courses', false);
    }

    private function update_lessons(){
        $filterData['date[0]'] = date('Y-m-d');
        $filterData['date[1]'] = date('Y-m-d', strtotime(date('Y-m-d') . '+2 years'));
        $this->MK->insertApiDataToDB('getLessons', 'mk_lessons', true, 'lessons','company/lessons', false, $filterData);
    }

    public function update_lesson_records()
    {
        $filterData['date[0]'] = date('Y-m-d');
        $filterData['date[1]'] = date('Y-m-d', strtotime(date('Y-m-d') . '+2 years'));
        $this->MK->insertApiDataToDB('getLessonRecords', 'mk_lesson_records', true, 'lessonRecords', 'company/lessonRecords', false, $filterData);
    }

    public function update_subscriptions()
    {
        $this->MK->insertApiDataToDB('getSubscriptions', 'mk_subscriptions', true, 'subscriptions', 'company/subscriptions',);
    }

    public function update_user_subscriptions()
    {
        $items = [];
        $keys = [];
        $tableName = 'mk_user_subscriptions';
        $MK = new MoyKlass();
        $filterData['sellDate[0]'] = date('Y-m-d', strtotime(date('Y-m-d') . '-4 days'));
        $filterData['sellDate[1]'] = date('Y-m-d');
        $userSubscriptions = $MK->getUserSubscriptions($filterData);

        if (!isset($userSubscriptions['subscriptions']))
            return;

        foreach (Model::getColumnValues($userSubscriptions['subscriptions'], Model::getInstance()->getTableColumn($tableName)) as $item) {
            $keys = array_keys($item);
            $items[] = $item;
        }

        $sql = Model::getInstance()->prepareBulkInsert($tableName, $keys, $items, true);

        DB::exec($sql);
    }

    public function update_users()
    {
        $this->MK->insertApiDataToDB('getUsers', 'mk_users', true, 'users', 'company/users',);
    }

    public function get_not_active_subscriptions()
    {
        $MK = new MoyKlass();
        $data = $MK->getAllDataFromApi('getUserSubscriptions', 'mk_user_subscriptions', 'subscriptions', ['statusId' => 1], 'company/userSubscriptions');

        $sql = "UPDATE mk_user_subscriptions SET statusId = 1 WHERE id in (" . implode(',', array_column($data, 'id')) . ")";

        DB::exec($sql);
    }

    public function update_user_subscriptions_data()
    {
        $MK = new MoyKlass();
        $data = $MK->getAllDataFromApi('getUserSubscriptions', 'mk_user_subscriptions', 'subscriptions', ['statusId' => 2], 'company/userSubscriptions');

        $sql = "DELETE FROM mk_user_subscriptions WHERE id in (" . implode(',', array_column($data, 'id')) . ")";
        DB::exec($sql);

        $sql = Model::getInstance()->prepareBulkInsert('mk_user_subscriptions', array_keys($data[0]), $data);
        DB::exec($sql);
    }

    public function get_frozen_subscriptions()
    {
        $MK = new MoyKlass();
        $data = $MK->getAllDataFromApi('getUserSubscriptions', 'mk_user_subscriptions', 'subscriptions', ['statusId' => 3], 'company/userSubscriptions');

        if (array_column($data, 'id')){
            $sql = "UPDATE mk_user_subscriptions SET statusId = 3 WHERE id in (" . implode(',', array_column($data, 'id')) . ")";
            DB::exec($sql);
        }
    }

    public function get_canceled_subscriptions()
    {
        $MK = new MoyKlass();
        $data = $MK->getAllDataFromApi('getUserSubscriptions', 'mk_user_subscriptions', 'subscriptions', ['statusId' => 4], 'company/userSubscriptions');

        $sql = "UPDATE mk_user_subscriptions SET statusId = 4 WHERE id in (" . implode(',', array_column($data, 'id')) . ")";

        DB::exec($sql);
    }

    public function update_users_in_GK()
    {
        $sql = "
            SELECT TIMESTAMPDIFF(HOUR, mu.last_update, CURRENT_TIMESTAMP()) as hour, mu.id, email FROM mk_users as mu
            LEFT JOIN mk_user_subscriptions as mus ON mu.id = mus.userId
            WHERE mus.statusId IN (1,2) AND email is not null
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


    public function update_users_in_GK_finished()
    {
        return;
        $sql = "
            SELECT TIMESTAMPDIFF(HOUR, mu.last_update, CURRENT_TIMESTAMP()) as hour, mu.id, email FROM mk_users as mu
            LEFT JOIN mk_user_subscriptions as mus ON mu.id = mus.userId
            WHERE mus.statusId IN (3,4) AND email is not null
            GROUP BY email
            HAVING hour > 24 OR hour IS null
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

    private function synchronizationchatmanagers_manual(){
        $ChatAdminModel = new ChatAdminModel();
        $ChatAdminModel->getSyncManagers();
    }
}