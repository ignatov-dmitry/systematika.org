<?php


namespace GKTOMK\Models;



use GKTOMK\Models\WhatsappApi\WAZZUPAPI;

class WhatsappModel
{

    private $Member;

    public function __construct()
    {
        DB::init();
        $this->Member = new MemberModel();
    }

    private function setStatusByRecordId($record_id, $status)
    {
        return $this->editWhatsapp([
            'record_id_mk' => $record_id,
            'status' => $status,
        ]);
    }

    private function editWhatsapp($data = [])
    {
        return DB::edit('whatsappmessages', $data);
    }

    public function sendMessages($records, $requestData)
    {
        foreach ($records as $record) {
            $this->whatsappHandle($record, $requestData);
        }
    }

    private function whatsappHandle($recordData, $requestData)
    {

        if(empty($recordData['lesson_id']))
            return;

        if(empty($recordData['phone'])){

            if($recordData['whatsapp_status']!=='nophone'){
                $this->editWhatsapp([
                    'member_id' => $recordData['user_id'],
                    'lesson_id_mk' => $recordData['lesson_id'],
                    'record_id_mk' => $recordData['record_id'],
                    'user_id_mk' => $recordData['user_id'],
                    'status' => 'nophone',
                    'date' => time(),
                ]);

                if(!empty($recordData['email'])){
                    $this->Member->is_not_found($recordData['email']);
                }
            }

            return;
        }

        $phone = HelperModel::onlyNumbers($recordData['phone']);
        $message = DB::getOption('systemsetting', 'whatsapp_message') ?: "Занятие сегодня
{*datestart*}
{*course_name*}. {*class_name*}
Вход {*topic*}";


        $GroupsModel = new GroupsModel();
        $groupsync = $GroupsModel->getGroupsyncByGroupIdMK($recordData['class_id']);
        $program = $GroupsModel->getProgramById($groupsync['program_id'])[0];
        $class = $GroupsModel->getClassById($groupsync['class_id'])[0];


        $programname = empty($program) ? $recordData['course_name'] : $program['name'];
        $classname = empty($class) ? $recordData['class_name'] : $class['name'];

        $datestart = date('d', $recordData['timestart']);
        $mnthtxt = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        $datestart .= ' ' . $mnthtxt[(date("n", $recordData['timestart'])-1)];
        $datestart .= ' в ' . $requestData['object']['beginTime'];

        $timeleft = HelperModel::timeleft($recordData['timestart'],
            ['days' => ['день', 'дня', 'дней'],
                'hours' => ['час','часа','часов'],
                'minutes' => ['минуту','минуты','минут']]
        );


        $ViewsModel = new ViewsModel();
        $ViewsModel->setVars([
            'first_name' => $recordData['first_name'],
            'last_name' => '',
            'class_name' => $classname,
            'course_name' => $programname,
            'topic' => $requestData['object']['topic'],
            'datestart' => $datestart,
            'timeleft' => $timeleft,
            'datesend' => date("d.m.Y в H:i"),
            'phone' => $phone,
        ]);
        $message = $ViewsModel->parseTplcode($message);

        $result = $this->sendWhatsapp($phone, $message);

        $this->editWhatsapp([
            'member_id' => $recordData['user_id'],
            'lesson_id_mk' => $recordData['lesson_id'],
            'record_id_mk' => $recordData['record_id'],
            'user_id_mk' => $recordData['user_id'],
            'phone' => $phone,
            'message' => $message,
            'status' => 'sent',
            'date' => time(),
            'log' => json_encode([$result])
        ]);

    }

    private function sendWhatsapp($phone, $message)
    {
        $userPhone = $phone;
        /*$data = [
            'phone' => $phone, // Телефон получателя
            'body' => $message, // Сообщение
        ];*/

        // Debug
        $debug = DB::getOption('systemsetting', 'whatsapp_debug') ? 1 : 0;
        if($debug==true)
            $phone = DB::getOption('systemsetting', 'whatsapp_phone') ?: '79014897145';

        $apitype = DB::getOption('systemsetting', 'whatsapp_typeapi');

        switch ($apitype){
            default:
            case 'chatapi':
                    return $this->sendApiChatapi($phone, $message);
                break;

            case 'wazzup':
                 return $this->sendApiWazzup($phone, $message, $userPhone);
                break;
        }
    }

    private function sendApiChatapi($phone, $message)
    {
        $data = [
            'phone' => $phone, // Телефон получателя
            'body' => $message, // Сообщение
        ];

        //print_r($data);

        $json = json_encode($data); // Закодируем данные в JSON
        // URL для запроса POST /message
        $token = '7x8rnwicxdjlvgf8';
        $instanceId = '316199';
        $url = 'https://api.chat-api.com/instance'.$instanceId.'/message?token='.$token;
        // Сформируем контекст обычного POST-запроса
        $options = stream_context_create(['http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/json',
            'content' => $json
        ]
        ]);
        // Отправим запрос
        return file_get_contents($url, false, $options);
    }

    private function sendApiWazzup($phone, $message, $userPhone)
    {
        $data = [
            'channelId' => 'a7d9355f-4d4b-452e-ad7d-d1348f64ea5f',
            'chatType' => 'whatsapp',
            'chatId' => $phone,
            'text' => $message
        ];
        return WAZZUPAPI::sendMessage($data);
    }
}
