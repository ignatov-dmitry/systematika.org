<?php


namespace GKTOMK\Models;



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

    private function getCron()
    {
        //  AND IF(`w`.`id`, `w`.`status`<>"sent", 1)
        $timeend = DB::getOption('systemsetting', 'whatsapp_time')
            ? (DB::getOption('systemsetting', 'whatsapp_time') * 60)
            : (60 * 60 * 2);

        return DB::getAll(
            'SELECT *, `rl`.`lesson_id_mk` `lesson_id_mk`, 
                `rl`.`record_id_mk` `record_id_mk`, 
                `rl`.`user_id_mk` `user_id_mk`, 
                `m`.`id` `member_id`,
                (SELECT `w`.`status` FROM `whatsappmessages` `w` WHERE `w`.`record_id_mk`=`rl`.`record_id_mk` ORDER by `w`.`date` DESC LIMIT 1) `whatsapp_status`
                FROM `lessons` `l` 
                 LEFT JOIN `recordslesson` `rl` ON `l`.`lesson_id_mk`=`rl`.`lesson_id_mk` 
                 LEFT JOIN `member` `m` ON `m`.`mk_uid`=`rl`.`user_id_mk` 
                 WHERE 
                 
                 (`l`.`timestart`>:timestart 
                 AND `l`.`timestart`<:timeend) 
                 GROUP BY `rl`.`record_id_mk`
                 HAVING `whatsapp_status` IS NULL or `whatsapp_status` <> "sent"
                 ORDER BY `timestart` ASC
                 ',
            [
                'timestart' => time(),
                'timeend' => time() + $timeend
            ]);
    }

    public function cronStart(){
        $records = $this->getCron();

        var_dump($records);


        foreach ($records as $record) {
            $this->cronWhatsappHandle($record);
        }
    }

    private function cronWhatsappHandle($recordData)
    {

        if(empty($recordData['lesson_id_mk']))
            return;

        if(empty($recordData['phone'])){

            if($recordData['whatsapp_status']!=='nophone'){
                $this->editWhatsapp([
                    'member_id' => $recordData['member_id'],
                    'lesson_id_mk' => $recordData['lesson_id_mk'],
                    'record_id_mk' => $recordData['record_id_mk'],
                    'user_id_mk' => $recordData['user_id_mk'],
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
        $groupsync = $GroupsModel->getGroupsyncByGroupIdMK($recordData['class_id_mk']);
        $program = $GroupsModel->getProgramById($groupsync['program_id'])[0];
        $class = $GroupsModel->getClassById($groupsync['class_id'])[0];


        $programname = empty($program) ? $recordData['course_name'] : $program['name'];
        $classname = empty($class) ? $recordData['class_name'] : $class['name'];
       
        //echo $recordData['record_id_mk'] . ' ' . $recordData['date'] . ' - '. $recordData['begin_time'] . ' ' .$programname . ' ' .$classname . ' - ' . $recordData['first_name'] . ' ' . $recordData['last_name'] . '      ' . $recordData['phone'] . PHP_EOL;


        $datestart = date('d', $recordData['timestart']);
        $mnthtxt = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];
        $datestart .= ' ' . $mnthtxt[(date("n", $recordData['timestart'])-1)];
        $datestart .= ' в ' . $recordData['begin_time'];

        $timeleft = HelperModel::timeleft($recordData['timestart'],
            ['days' => ['день', 'дня', 'дней'],
                'hours' => ['час','часа','часов'],
                'minutes' => ['минуту','минуты','минут']]
        );


        $ViewsModel = new ViewsModel();
        $ViewsModel->setVars([
            'first_name' => $recordData['first_name'],
            'last_name' => $recordData['last_name'],
            'class_name' => $classname,
            'course_name' => $programname,
            'topic' => $recordData['topic'],
            'datestart' => $datestart,
            'timeleft' => $timeleft,
            'datesend' => date("d.m.Y в H:i"),
            'phone' => $phone,
        ]);
        $message = $ViewsModel->parseTplcode($message);

        $result = $this->sendWhatsapp($phone, $message);

        //var_dump($result);

        $this->editWhatsapp([
            'member_id' => $recordData['member_id'],
            'lesson_id_mk' => $recordData['lesson_id_mk'],
            'record_id_mk' => $recordData['record_id_mk'],
            'user_id_mk' => $recordData['user_id_mk'],
            'phone' => $phone,
            'message' => $message,
            'status' => 'sent',
            'date' => time(),
            'log' => json_encode([$result])
        ]);

    }

    private function sendWhatsapp($phone, $message)
    {
        /*$data = [
            'phone' => $phone, // Телефон получателя
            'body' => $message, // Сообщение
        ];*/

        // Debug
        $debug = DB::getOption('systemsetting', 'whatsapp_debug') ? 1 : 0;
        if($debug==true)
            $phone = DB::getOption('systemsetting', 'whatsapp_phone') ?: '79014897145';
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



}