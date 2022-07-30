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
        ]);
    }

    public function getFindDialog($manager_member_id, $client_member_id)
    {
        $dialog = DB::getRow('SELECT `id`,`banned`,`calladmin` FROM `chatdialogs` WHERE `manager_member_id`=? && `client_member_id`=? LIMIT 1', [$manager_member_id, $client_member_id]);
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

    public function getDownloadAttachmentByMessageId($message_id){

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

            

        }
        return $getDialog;
    }

    public function setDialog($dialog_id, $param, $value)
    {
        return DB::edit('chatdialogs', [
            'id' => $dialog_id,
            "{$param}" => $value
        ]);
    }

    public function uploadAttachment($dialog_id){

        $data = $this->uploadFile($dialog_id);

        if(empty($data['error'])){
            $data['attachment_id'] = $this->addAttachmentByDialogId($dialog_id, $data['attachment_name'], $data['attachment_realname']);
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
        if (!isset($_FILES[$input_name])) {
            $error = 'Файл не загружен.';
        } else {
            $file = $_FILES[$input_name];

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
        }


        // Вывод сообщения о результате загрузки.
        if (!empty($error)) {
            $error = '' . $error . '';
        }

        $data = array(
            'error'   => $error,
            'success' => $success,
            'attachment_name' => $name,
            'attachment_realname' => $newname,
        );

        return $data;

    }


}