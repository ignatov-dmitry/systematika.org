<?php


namespace GKTOMK\Models\Events;


use GKTOMK\Models\ChatModels\ChatAdminModel;
use GKTOMK\Models\Events;
use GKTOMK\Models\HandlerHwkModel;
use GKTOMK\Models\LessonsModel;
use GKTOMK\Models\MissingTrialModel;
use GKTOMK\Models\StatisticsModel;
use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\VideorecordsModel;
use GKTOMK\Models\WebhookModel;
use GKTOMK\Models\WhatsappModel;
use GKTOMK\Models\ZoomModel;

class CronEvents extends Events
{


    public function __construct($request)
    {
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
        $WhatsappModel = new WhatsappModel();
        $WhatsappModel->cronStart();
    }

    private function synchronizationlessons_manual(){
        $LessonsModel = new LessonsModel();
        $LessonsModel->setSynchronizationByDate(time(), time());
    }

    private function webhook_every1minute(){
        $WebhookModel = new WebhookModel();
        $WebhookModel->cronStart();
    }

    private function synchronizationchatmanagers_manual(){
        $ChatAdminModel = new ChatAdminModel();
        $ChatAdminModel->getSyncManagers();
    }
}