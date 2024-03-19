<?php


namespace GKTOMK\Models\ChatModels;


use GKTOMK\Models\DB;
use GKTOMK\Models\GroupsModel;
use GKTOMK\Models\LessonsModel;

class ChatModel
{

    public function __construct()
    {
        DB::init();
    }

    public function createDialog($manager_member_id, $client_member_id)
    {
        return DB::edit('chatdialogs', [
            'date_create' => time(),
            'client_member_id' => $client_member_id,
            'manager_member_id' => $manager_member_id,
            'banned' => 0,
            'calladmin' => 0,
            'date_update' => time(),
        ]);
    }

    public function getFindDialog($manager_member_id, $client_member_id)
    {
        $dialog = DB::getRow('SELECT `id`,`banned`,`calladmin`,`date_update` FROM `chatdialogs` WHERE `manager_member_id`=? && `client_member_id`=? LIMIT 1', [$manager_member_id, $client_member_id]);
        if (empty($dialog)) {
            $id = $this->createDialog($manager_member_id, $client_member_id);
            return ['id' => $id, 'banned' => 0, 'calladmin' => 0];
        }
        return $dialog;
    }

    public function getLoadMessages($manager_member_id, $client_member_id)
    {
        $dialog = $this->getFindDialog($manager_member_id, $client_member_id);
        return ['dialog_id' => $dialog['id'], 'banned' => $dialog['banned'], 'calladmin' => $dialog['calladmin'],  'dialog_info' => $this->getDialogInfo($dialog['id']), 'messages' => $this->getMessagesByDialogId($dialog['id'])];

    }

    public function getMessagesByDialogId($dialog_id)
    {
        return DB::getAllByKey('chatmessages', 'chatdialog_id', $dialog_id, ['*']);
    }

    public function addMessageByMemberIdAndDialogId($from_member_id, $dialog_id, $messageData = [])
    {
        $this->setDialog($dialog_id, 'date_update_client', time());
        $this->setDialog($dialog_id, 'date_update_admin', time());
        $this->setDialog($dialog_id, 'date_update_manager', time());
        return DB::edit('chatmessages', [
            'chatdialog_id' => $dialog_id,
            'from_member_id' => $from_member_id,
            'message' => $messageData['message'],
            'attachment_id' => $messageData['attachment_id'],
            'read' => 0,
            'time' => time(),
        ]);
    }



    public function addAttachmentByDialogId($dialog_id, $name, $realname)
    {
        return DB::edit('chatattachments', [
            'chatdialog_id' => $dialog_id,
            'name' => $name,
            'realname' => $realname,
            'time' => time(),
        ]);
    }

    public function getDownloadAttachmentByMessageId($message_id)
    {
        $message = $this->getMessageById($message_id);
        $attach = $this->getAttachmentById($message['attachment_id']);
        if(!empty($attach['id'])){
            echo $pathfile = __DIR__ . '/../../../uploads/chat/dialog_'.$attach['chatdialog_id'].'/'.$attach['realname'];
            $res = pathinfo($pathfile);
            $this->outputAttachFile(
                $pathfile,
                $attach['name']);
        }
    }

    private function outputAttachFile($pathfile, $name)
    {
        if (file_exists($pathfile)) {
            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }

            // заставляем браузер показать окно сохранения файла
            //header('Content-Description: File Transfer');
            header('Content-Type: ' . mime_content_type($pathfile));
            //header('Content-Disposition: attachment; filename=' . $name);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($pathfile));

            exit(readfile($pathfile));
        }
    }

    public function getMessageById($message_id){
        return DB::getRowByKey('chatmessages', 'id', $message_id, ['attachment_id']);
    }

    public function getAttachmentById($attach_id){
        return DB::getRowByKey('chatattachments', 'id', $attach_id, ['chatdialog_id', 'name', 'realname']);
    }

    public function getLastMessage($dialog_id)
    {
        //return
    }

    /**
     * Устанавливает группы для диалога которые их связывают
     * */
    protected function setDialogsGroups($data_dialogs){
        foreach ($data_dialogs as $dialog) {
            DB::delete('chatgroups', 'chatdialog_id', $dialog['dialog_id']);
            if(!empty($dialog['groups']))
                foreach ($dialog['groups'] as $group) {
                    DB::edit('chatgroups', [
                        'chatdialog_id' => $dialog['dialog_id'],
                        'group_id' => $group['groupsync']['group_id_mk']
                    ]);
                }
        }
    }


    private function getDialogGroups($dialog_id){
        return DB::getAllByKey('chatgroups', 'chatdialog_id', $dialog_id, ['group_id']);
    }

    /**
     * Отдавет подробную информацию о диалоге
     * */
    public function getDialogInfo($dialog_id)
    {
        $getDialog = DB::getRowByKey('chatdialogs', 'id', $dialog_id, ['*']);
        if(!empty($getDialog)){
            $getDialog['client_member'] = DB::getRowByKey('member', 'id', $getDialog['client_member_id'], ['first_name', 'last_name', 'gk_uid', 'foto_url']);
            $getDialog['manager_member'] = DB::getRowByKey('member', 'id', $getDialog['manager_member_id'], ['first_name', 'last_name', 'gk_uid', 'foto_url']);

            $groups = $this->getDialogGroups($dialog_id);
            //var_dump($groups);
            $i = 0;
            foreach ($groups as $group) {
                $groupsync = DB::getRowByKey('groupsync', 'group_id_mk', $group['group_id'], ['group_id_mk','program_id','class_id','individual','begin_date']);
                $getDialog['groups'][] = [
                    'groupsync' => $groupsync,
                    'class' => DB::getRowByKey('class', 'id', $groupsync['class_id'], ['name']),
                    'program' => DB::getRowByKey('program', 'id', $groupsync['program_id'], ['name'])
                ];
            }

            $getDialog['dialog_name'] = $getDialog['manager_member']['last_name'] . ' ' .$getDialog['manager_member']['first_name']
                . ' - ' .$getDialog['client_member']['last_name'] . ' ' . $getDialog['client_member']['first_name'];



            $getDialog['dialog_id'] = $getDialog['id'];
            $getDialog['count_unread_messages_client'] = DB::count('chatmessages', 'WHERE `read`<>1 && `chatdialog_id`=:chatdialog_id && `from_member_id`<>:client_member_id', ['chatdialog_id' => $getDialog['id'], 'client_member_id'=>$getDialog['client_member']['id']]);
            $getDialog['count_unread_messages_manager'] = DB::count('chatmessages', 'WHERE `read`<>1 && `chatdialog_id`=:chatdialog_id && `from_member_id`=:client_member_id', ['chatdialog_id' => $getDialog['id'], 'client_member_id'=>$getDialog['client_member']['id']]);
            $getDialog['lastmessage_time'] = DB::getRowByKey('chatmessages', 'chatdialog_id', $getDialog['id'], ['time'], 'ORDER BY `id` DESC LIMIT 1')['time'];




        }
        return $getDialog;
    }

    public function getDialogsByClientMemberId($client_member_id){
        return DB::getAllByKey('chatdialogs', 'client_member_id', $client_member_id, ['*']);
    }

    public function getDialogsByManagerMemberId($client_member_id){
        return DB::getAllByKey('chatdialogs', 'manager_member_id', $client_member_id, ['*']);
    }

    public function setDialog($dialog_id, $param = null, $value = null)
    {
        $data = [
            'id' => $dialog_id,
            'date_update' => time(),
        ];
        if(!empty($param)){
            $data[$param] = $value;
        }
        return DB::edit('chatdialogs', $data);
    }

    public function setReadMessagesByDialogIdAndFromId($dialog_id, $from_id){
        //$this->setDialog($dialog_id);
        return DB::exec('UPDATE `chatmessages` SET `read`=1 WHERE `from_member_id`<>:from_id && `chatdialog_id`=:dialog_id',
            [
                'dialog_id' => $dialog_id,
                'from_id' => $from_id
            ]);
    }

    public function uploadAttachment($dialog_id){

        $data = $this->uploadFile($dialog_id);

        for ($i = 0; $i < count($data); $i++)
        {
            if(empty($data[$i]['error'])){
                $data[$i]['attachment_id'] = $this->addAttachmentByDialogId($dialog_id, $data[$i]['attachment_name'], $data[$i]['attachment_realname']);
            }
        }
        return $data;
    }

    public function uploadFile($dialog_id){

        // Название <input type="file">
        $input_name = 'file';

// Разрешенные расширения файлов.
        $allow = array();

// Запрещенные расширения файлов.
        $deny = array(
            'phtml', 'php', 'php3', 'php4', 'php5', 'php6', 'php7', 'phps', 'cgi', 'pl', 'asp',
            'aspx', 'shtml', 'shtm', 'htaccess', 'htpasswd', 'ini', 'log', 'sh', 'js', 'html',
            'htm', 'css', 'sql', 'spl', 'scgi', 'fcgi', 'exe'
        );

// Директория куда будут загружаться файлы.

        $dir_chat = __DIR__ . '/../../../uploads/chat';
        if(!file_exists($dir_chat) and !mkdir($dir_chat))
            $error = 'Ошибка при создании директории чата.';

        $path = $dir_chat . '/dialog_'.$dialog_id;

        if(!file_exists($path) and !mkdir($path))
            $error = 'Ошибка при создании директории диалога.';


        $error = $success = '';
        if (!isset($_FILES)) {
            $error = 'Файл не загружен.';
        } else {
            $files = $_FILES;
            foreach ($files as $file)
            {

                // Проверим на ошибки загрузки.
                if (!empty($file['error']) || empty($file['tmp_name'])) {
                    $error = 'Не удалось загрузить файл.';
                } elseif ($file['tmp_name'] == 'none' || !is_uploaded_file($file['tmp_name'])) {
                    $error = 'Не удалось загрузить файл.';
                } else {
                    // Оставляем в имени файла только буквы, цифры и некоторые символы.
                    $pattern = "[^a-zа-яё0-9,~!@#%^-_\$\?\(\)\{\}\[\]\.]";
                    $name = mb_eregi_replace($pattern, '-', $file['name']);
                    $name = mb_ereg_replace('[-]+', '-', $name);
                    $parts = pathinfo($name);

                    if (empty($name) || empty($parts['extension'])) {
                        $error = 'Недопустимый тип файла';
                    } elseif (!empty($allow) && !in_array(strtolower($parts['extension']), $allow)) {
                        $error = 'Недопустимый тип файла';
                    } elseif (!empty($deny) && in_array(strtolower($parts['extension']), $deny)) {
                        $error = 'Недопустимый тип файла';
                    } elseif($file['size'] > (8 * 1024 * 1024 * 50)) {
                        $error = 'Слишком большой размер ('.$file['size'].') файла';
                    }else{
                        // Перемещаем файл в директорию.
                        $newname = md5($name . rand(9999, 999999)) .'.'. $parts['extension'];
                        if (move_uploaded_file($file['tmp_name'], $path .'/'. $newname)) {
                            // Далее можно сохранить название файла в БД и т.п.
                            $success = 'Файл «' . $name . '» успешно загружен.';
                        } else {
                            $error = 'Не удалось загрузить файл.';
                        }
                    }
                }

                $data[] = array(
                    'error'   => $error,
                    'success' => $success,
                    'attachment_name' => $name,
                    'attachment_realname' => $newname,
                );
            }
        }


        // Вывод сообщения о результате загрузки.
        if (!empty($error)) {
            $error = '' . $error . '';
        }

        return $data;

    }


}