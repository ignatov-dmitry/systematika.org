<?php


namespace GKTOMK\Models\ChatModels;


use GKTOMK\Models\DB;
use GKTOMK\Models\GroupsModel;
use GKTOMK\Models\LessonsModel;

class ChatUserModel extends ChatModel
{

    public function __construct()
    {
        parent::__construct();
        DB::init();
    }

    public function getClientMemberByMkUid($mk_uid){
        return DB::getRowByKey('member', 'mk_uid', $mk_uid, ['first_name', 'last_name', 'gk_uid', 'foto_url']);
    }

    public function getManagerMemberByMkManagerId($mk_manager_id){
        return DB::getRowByKey('member', 'mk_manager_id', $mk_manager_id, ['first_name', 'last_name', 'gk_uid', 'foto_url']);
    }

    public function getClassesUserByMkUserId($mk_uid)
    {
        $LessonsModel = new LessonsModel();
        $lessons = $LessonsModel->getLessonsByUserIdMKAndTime($mk_uid);
        print_r($lessons);
        $groupsUniq = [];
        $GroupsModel = new GroupsModel();
        foreach ($lessons as $lesson) {

            $groupsync = $GroupsModel->getGroupsyncByGroupIdMK($lesson['class_id_mk']);

            if(empty($groupsync))
                continue;
            print_r($groupsync);
            if($groupsync['individual']==true){
                $time = 60 * 60 * 24 * 365;  // Индивидуальное
            }else{
                $time = 60 * 60 * 24 * 45; // Если групповое
            }

            $timestart = time() - $time;

            // Убираем из списка занятия, которые не подходят по фильтру
            echo $lesson['timestart'].'--';
            echo $timestart;

            if($lesson['timestart'] < $timestart)
                continue;

            echo 'hren';

            $groupsUniq[] = [
                'class_id_mk' => $lesson['class_id_mk'],
                'groupsync' => $groupsync
            ];
        }

        print_r($groupsUniq);
        return $groupsUniq;
    }






   public function getUserDialogsByUserIdMK($user_id_mk)
    {

        $client_member = $this->getClientMemberByMkUid($user_id_mk);

        $groups = $this->getClassesUserByMkUserId($user_id_mk);
        $teachersUniq = [];
        //$GroupsModel = new GroupsModel();
        foreach ($groups as $group) {

            //$groupsync = $GroupsModel->getGroupsyncByGroupIdMK($group);
            $groupsync = $group['groupsync'];

            if (empty($groupsync) or empty($groupsync['manager_ids'])) {
                continue;
            }


            $teachers = json_decode($groupsync['manager_ids'], 1);
            foreach ($teachers as $teacher) {
                $dataTeach = [];


                if (empty($teachersUniqDump[$teacher]['class_ids']))
                    $teachersUniqDump[$teacher]['class_ids'] = [];

                if (!in_array($group, $teachersUniqDump[$teacher]['class_ids'])) {

                    $teachersUniq[$teacher]['manager_id'] = $teacher;

                    $manager_member = $this->getManagerMemberByMkManagerId($teacher);
                    if(empty($manager_member))
                        continue;

                    $teachersUniq[$teacher]['manager_member'] = $manager_member;
                    //$teachersUniq[$teacher]['client_member'] = $client_member;

                    $teachersUniq[$teacher]['dialog_name'] = $manager_member['last_name'] . ' ' .$manager_member['first_name']
                        . ' - ' .$client_member['last_name'] . ' ' . $client_member['first_name'];


                    $dataTeach['groupsync'] = $groupsync;
                    $dataTeach['class'] = DB::getRowByKey('class', 'id', $groupsync['class_id'], ['name']);
                    $dataTeach['program'] = DB::getRowByKey('program', 'id', $groupsync['program_id'], ['name']);
                    $getDialog = $this->getFindDialog($manager_member['id'], $client_member['id']);

                    $teachersUniq[$teacher]['dialog_id'] = $getDialog['id'];
                    $teachersUniq[$teacher]['banned'] = $getDialog['banned'];
                    $teachersUniq[$teacher]['calladmin'] = $getDialog['calladmin'];
                    $teachersUniq[$teacher]['count_unread_messages'] = DB::count('chatmessages', 'WHERE `read`<>1 && `chatdialog_id`=:chatdialog_id', ['chatdialog_id' => $getDialog['id']]);
                    $teachersUniq[$teacher]['lastmessage_time'] = DB::getRowByKey('chatmessages', 'chatdialog_id', $getDialog['id'], ['time'], 'ORDER BY `id` DESC LIMIT 1')['time'];


                    array_push($teachersUniqDump[$teacher]['class_ids'], $group);
                    $teachersUniq[$teacher]['groups'][] = $dataTeach;
                }

            }


        }


        // Сохраняем связи для диалога
        $this->setDialogsGroups($teachersUniq);


        return $teachersUniq;
    }


    private function getGroupsByUserIdMK($user_id_mk){
        return DB::getAll('SELECT `l`.`class_id_mk`,`g`.* FROM `recordslesson` `rl` 
                            LEFT JOIN `lessons` `l` ON `rl`.`lesson_id_mk`=`l`.`lesson_id_mk`
                            LEFT JOIN `groupsync` `g` ON `g`.`group_id_mk`=`l`.`class_id_mk`
                            WHERE `user_id_mk`=:user_id_mk
                            && (IF(`g`.`individual`, (`l`.`timestart`>1000), (`l`.`timestart`>=0)))
                            GROUP BY `l`.`class_id_mk`', ['user_id_mk' => $user_id_mk]);
    }

    /*public function getUserDialogsByUserIdMK($user_id_mk)
    {

        $client_member = $this->getClientMemberByMkUid($user_id_mk);

        $groups = $this->getGroupsByUserIdMK($user_id_mk);
        $teachersUniq = [];

        foreach ($groups as $group) {

            if (empty($group['group_id_mk']) or empty($group['manager_ids'])) {
                continue;
            }


            $teachers = json_decode($group['manager_ids'], 1);
            foreach ($teachers as $teacher) {
                $dataTeach = [];


                if (empty($teachersUniqDump[$teacher]['class_ids']))
                    $teachersUniqDump[$teacher]['class_ids'] = [];

                if (!in_array($group, $teachersUniqDump[$teacher]['class_ids'])) {

                    $teachersUniq[$teacher]['manager_id'] = $teacher;

                    $manager_member = $this->getManagerMemberByMkManagerId($teacher);
                    if(empty($manager_member))
                        continue;

                    $teachersUniq[$teacher]['manager_member'] = $manager_member;
                    //$teachersUniq[$teacher]['client_member'] = $client_member;

                    $teachersUniq[$teacher]['dialog_name'] = $manager_member['last_name'] . ' ' .$manager_member['first_name']
                        . ' - ' .$client_member['last_name'] . ' ' . $client_member['first_name'];


                    $dataTeach['groupsync'] = $group;
                    $dataTeach['class'] = DB::getRowByKey('class', 'id', $group['class_id'], ['name']);
                    $dataTeach['program'] = DB::getRowByKey('program', 'id', $group['program_id'], ['name']);
                    $getDialog = $this->getFindDialog($manager_member['id'], $client_member['id']);

                    $teachersUniq[$teacher]['dialog_id'] = $getDialog['id'];
                    $teachersUniq[$teacher]['banned'] = $getDialog['banned'];
                    $teachersUniq[$teacher]['calladmin'] = $getDialog['calladmin'];
                    $teachersUniq[$teacher]['lastmessage_time'] = DB::getRowByKey('chatmessages', 'chatdialog_id', $getDialog['id'], ['time'], 'ORDER BY `id` DESC LIMIT 1')['time'];


                    array_push($teachersUniqDump[$teacher]['class_ids'], $group);
                    $teachersUniq[$teacher]['groups'][] = $dataTeach;
                }

            }


        }
        // Сохраняем связи для диалога
        $this->setDialogsGroups($teachersUniq);


        return $teachersUniq;
    }*/

}