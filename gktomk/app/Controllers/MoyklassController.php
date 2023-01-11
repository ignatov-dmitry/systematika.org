<?php

namespace GKTOMK\Controllers;
$timer = microtime(true);
use GKTOMK\Models\EventsMoyklass;
use GKTOMK\Models\HandlerHwkModel;
use GKTOMK\Models\HomeworkModel;
use GKTOMK\Models\MissingTrialModel;
use GKTOMK\Models\WebhookModel;

class MoyklassController extends Controller
{

    /**
     * @var HomeworkModel
     */
    private $Hwk;

    function __construct()
    {
        $this->Hwk = new HomeworkModel();

    }

    public function main()
    {
        $input = json_decode(
            file_get_contents('php://input'), true
        );

        $WebhookModel = new WebhookModel();
        $WebhookID = $WebhookModel->editLogWebhook(['event' => $input['event'],'request' => json_encode($input), 'date_create' => time(), 'status' => 'new']);

        $this->writeToLog($input, 'Дебаг MK. Получение данных - php/input', 'mk');

        if (!empty($input['event']) and ($input['event'] == 'lesson_record_new' or $input['event'] == 'lesson_record_changed') and ($input['object']['visit'] and $input['object']['visit'] == 1)) {
            ///$this->Hwk->createHwk($input['object']);

            // Добавляем занятие на проверку пропусков. Даелаем отметку в гк, если человек пропустил занятие
            $MissingTrial = new MissingTrialModel();
            $MissingTrial->addMissing($input['object']['lessonId']);
        }

        // Запускаем событие
        $EventMoyklass = new EventsMoyklass($input);
        $res = $EventMoyklass->handle();

        $this->writeToLog([$res], 'Event handle', 'mk');
        $WebhookModel->editLogWebhook(['id' => $WebhookID, 'date_loaded' => time(), 'status' => 'loaded']);


    }

    public function getCron()
    {
        $HandlerHwkModel = new HandlerHwkModel();
        $HandlerHwkModel->cronHandle();
    }

    public function getTest()
    {
        /*$dateStart = '2020-09-21';
        $dateFinish = '2020-10-04';

        $HandlerHwkModel = new HandlerHwkModel();

        $this->genTime('startHandlerByDate');
        $res = $HandlerHwkModel->startHandlerByDate($dateStart, $dateFinish);
        echo 'Время выполнения startHandlerByDate:' . $this->genTime('startHandlerByDate');
        var_dump($res);*/

        /*


                $LeadsModel = new LeadsModel();
                $find = $LeadsModel->getFindUserByEmail('max@namer.ru');

                print_r($find);*/


// Запускаем событие
        $EventMoyklass = new EventsMoyklass([
            'event' => 'lesson_record_changed',
            'object' => [
                'userId' => '810013',
            ]
        ]);
        $res = $EventMoyklass->handle();

    }


    public function getUpdateCountSubscriptions()
    {
        $HandlerHwkModel = new HandlerHwkModel();
        $res = $HandlerHwkModel->startCountUserSubscriptions();

        var_dump($res);
    }


}

$endtimer = round(microtime(true) - $timer, 4);
@writeToLog($_SERVER, 'Webhook, время загрузки: '.$endtimer, 'webhook');