<?php


namespace GKTOMK\Models;


class VideorecordsModel
{
    public function __construct()
    {
        DB::init();
    }

    public function getLoadOldLesson()
    {
        $lessons = MoyklassModel::getLessons([
            'date[0]' => '2021.09.13',
            'date[1]' => '2021.10.23',
            'limit' => 1000,
        ]);

        print_r($lessons);

        /* $int = 0;
         $lessonModel = new LessonsModel();
         foreach ($lessons['lessons'] as $lesson) {
             echo $int++ . '<br/>';
             $this->editRecord([
                 'lesson_id_mk' => $lesson['id'],
                 'timeend' => strtotime($lesson['date'] .' ' . $lesson['endTime']),
                 'status' => 'new',
             ]);
             echo $lesson['id'].'<br/>';
             print_r($lesson);
             $lessonModel->editLesson($lesson);
         }*/

    }

    /*
     * Загрузить старые уроки с определенныой даты
     * */

    public function delRecord($recordId)
    {
        DB::trash('videorecords', [$recordId]);
    }

    public function getAllRecords()
    {
        return DB::getAll('SELECT *, `vr`.`id` `id`, `vr`.`status` `status` FROM `videorecords` `vr`, `lessons` `l` WHERE (`vr`.`lesson_id_mk`=`l`.`lesson_id_mk`) && `timestart`<=:timenow  ORDER by `vr`.`timeend` DESC', ['timenow' => time()]);
    }

    public function cronStart()
    {

        $records = $this->getReadyRecordsForDownloads();
        var_dump($records);

        foreach ($records as $record) {
            $res = $this->downloadRecordById($record['id']);
            var_dump($res);
        }

    }

    /** функция которая отбирает записи готовые для попытки загрузить видео
     * Правила отбора:
     * 1. Новые, не обработанные записи спустя 10 минут после окончания урока
     * 2. Записи в статусе на перезагрузку каждые 10 мин после окончания урока в течение двух часов
     * */
    public function getReadyRecordsForDownloads() // Отдает готовые записи для скачивания ссылок
    {


        /*return DB::getAll(
            "SELECT * FROM `videorecords`
                WHERE
                (`status`='new' && `timeend`<=:time)
                or
                (`status` <> 'new' && `status` <> 'OK' && (`try_lasttime`<=:time && `try_lasttime`<(`timeend`*(60*60*3))))
                ORDER BY `date_create` DESC LIMIT 1",
            [
                'time' => (time() - 60 * 10), // Спустя 10 мин

            ]
        );*/
        return DB::getAll(
            "SELECT * FROM `videorecords` 
                WHERE 
                (`status`='new' && `timeend`<=(:timenow-(60*10))) 
                or 
                (
                    `status` <> 'new' && `status` <> 'OK' 
                    && 
                    (
                        (`try_timelast`<=(:timenow-60*10) && 
                                `timeend`<=(:timenow-60*10)
                                &&
                                `timeend`>=(:timenow-60*60*3)
                                )
                        or 
                            (`try_timelast`<=(:timenow-60*10) && 
                                `timeend`<=(:timenow-60*10)
                                &&
                                `try_num` < 3
                            ) 
                    )
                ) 
                ORDER BY  `timeend` DESC, `try_timelast` ASC  LIMIT 1",
            ['timenow' => time()]
        );
    }

    public function downloadRecordById($recordId)
    {
        $getRecord = $this->getRecordById($recordId);

        //var_dump($getRecord);

        if (empty($getRecord)) {
            return $this->setStatusRecordById($recordId, 'recordnotfound');
        }

        # Указываем какая это попытка загрузки
        $try = $getRecord['try_num'] + 1;
        $this->setDataRecord($recordId, 'try_num', $try);
        $this->setDataRecord($recordId, 'try_timelast', time()); // Время последней попытки загрузить видео

        return $this->downloadByLessonId($getRecord['lesson_id_mk'], $recordId);
    }

    public function getRecordById($recordId)
    {
        return DB::getRow('SELECT * FROM `videorecords` WHERE `id`=? LIMIT 1', [$recordId]);
    }

    public function setStatusRecordById($recordId, $status)
    {
        $this->setDataRecord($recordId, 'status', $status);
        return $status;
    }

    private function setDataRecord($recordId, $key, $value)
    {
        $record = DB::load('videorecords', $recordId);
        $record->{$key} = $value;
        DB::store($record);
    }

    public function downloadByLessonId($lessonId, $recordId = 0)
    {
        $LessonsModel = new LessonsModel();
        $LessonData = $LessonsModel->getLessonByLessonIdMK($lessonId);

        if (empty($LessonData)) {
            if (!empty($recordId))
                return $this->setStatusRecordById($recordId, 'lessonnotfound');
        }

        $ZoomaccountsModel = new ZoomaccountsModel();
        $getAccountId = $ZoomaccountsModel->getAccountIdByLessonIdMK($lessonId);
        if (empty($getAccountId)) {
            if (!empty($recordId))
                return $this->setStatusRecordById($recordId, 'accountnotfound');
        }

        $result = $this->downloadByGroupNameAndDate($getAccountId, $LessonData['class_name'], $LessonData['date'], $recordId);
        if ($result == 'OK')
            $LessonsModel->setDataLesson([
                'id' => $LessonData['id'],
                'videorecord' => 1,
            ]);
        return $result;

    }

    public function downloadByGroupNameAndDate($accountId, $groupName, $date, $recordId = 0)
    {
        if (empty(CONFIG['zoom_api']['key']) or empty(CONFIG['zoom_api']['secret'])) {
            if (!empty($recordId))
                return $this->setStatusRecordById($recordId, 'keysecretempty');
        }

        $ZoomModel = new ZoomModel();
        $meetings = $ZoomModel->getRecordings($accountId,
            [
                'from' => $date,
                'to' => $date
            ]);

        //print_r($meetings);


        $searchMeeting = [];
        // echo $groupName;
        foreach ($meetings['meetings'] as $meeting) {
            //   print_r($meeting);
            if (strpos($meeting['topic'], $groupName)) {
                $searchMeeting = $meeting;
            }
        }

        //print_r($meetings);

        if (empty($searchMeeting)) { // Мероприятие не найдено
            return $this->setStatusRecordById($recordId, 'notfoundmeeting');
        }

        $searchRecordings = [];
        foreach ($searchMeeting['recording_files'] as $recording_file) {
            $searchRecordings[$recording_file['recording_type']] = $recording_file;
        }

        if (empty($searchRecordings)) { // Записи не найдены
            return $this->setStatusRecordById($recordId, 'notfoundrecording');
        }

        $videoload = '';
        if (!empty($searchRecordings['shared_screen_with_speaker_view'])) {
            $videoload = 'shared_screen_with_speaker_view';
        } elseif (!empty($searchRecordings['shared_screen_with_gallery_view'])) {
            $videoload = 'shared_screen_with_gallery_view';
        } elseif (!empty($searchRecordings['active_speaker'])) {
            $videoload = 'active_speaker';
        } elseif (!empty($searchRecordings['shared_screen'])) {
            $videoload = 'shared_screen';
        }

        if (empty($videoload) or empty($searchRecordings[$videoload])) {
            return $this->setStatusRecordById($recordId, 'notfoundvideorecording_type');
        }

        if ($searchRecordings[$videoload]['status'] !== 'completed') {
            return $this->setStatusRecordById($recordId, 'notcompleted');
        }


        $this->editRecord([
            'id' => $recordId,
            'meeting_topic' => $searchMeeting['topic'],
        ]);

        $this->setStatusRecordById($recordId, 'startdownload');
        $getLink = $ZoomModel->getLinkDownloadByUrl($searchRecordings[$videoload]['download_url']);
        //var_dump($getLink);

        $time = strtotime($date);
        $Y = date("Y", $time);
        $m = date("m", $time);
        $d = date("d", $time);

        $dir = $Y . '/' . $m . '/' . $d;

        $result = $this->downloadByLink($getLink, 'videorecord/' . $dir, $groupName);

        $this->setDataRecord($recordId, 'download_url', $searchRecordings[$videoload]['download_url']);
        $this->setStatusRecordById($recordId, $result);
        return $result;


    }

    public function editRecord($data = [])
    {
        if (empty($data['id'])) { // Создание новой заявки
            // Ищем заявку по id урока, если она есть, то ничего не делаем
            $get = $this->getRecordByLessonId($data['lesson_id_mk']);
            if (!empty($get))
                return;
            else {
                $record = DB::dispense('videorecords');
                $record->date_create = time();
            }

        }
        if (!empty($data['id']))
            $record = DB::load('videorecords', $data['id']);

        foreach ($data as $key => $value) {
            $record->{$key} = $value;
        }
        return DB::store($record);

    }

    public function getRecordByLessonId($lessonId)
    {
        return DB::getRow('SELECT * FROM `videorecords` WHERE `lesson_id_mk`=? LIMIT 1', [$lessonId]);
    }

    private function downloadByLink($link, $dirName = '', $fileName = '', $suffix_script = '')
    {
        $dir = __DIR__ . '/../../';
        $dirToFile = $dir . $dirName;

        if (!file_exists($dirToFile) and is_writable($dir)) {
            if (!mkdir($dirToFile, 0777, true))
                return 'Error create dir';
        } elseif (!file_exists($dirToFile) and !is_writable($dir)) {
            return 'Dir not found or not writable.';
        }
        $fileName = $fileName . '.mp4';
        $cmd = "cd {$dirToFile}; curl -o '{$fileName}' -k '{$link}'; echo 'download'; > /dev/null";
        $res = shell_exec($cmd);

        $dirFile = $dirToFile . '/' . $fileName;
        if (file_exists($dirFile))
            return 'OK';
        else
            return 'ERR';

    }

}