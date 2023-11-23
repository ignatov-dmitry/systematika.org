<?php


namespace GKTOMK\Models\ChatModels;

use GKTOMK\Models\DB;
use GKTOMK\Models\GroupsModel;
use GKTOMK\Models\MemberModel;

class ChatTeacherModel extends ChatModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Получить группы в которых ведет преподаватель
     * */
    public function getClasses($manager_id){
        return DB::getAll('SELECT `group_id_mk`,`program_id`,`class_id`,`individual`,`begin_date` FROM `groupsync` WHERE `manager_ids` LIKE :manager_id ', [
            'manager_id' => '%"'.$manager_id.'"%'
        ]);
    }

    /**
     * Получить список уроков и учеников, которые учатся в группе
     * Фильтр по времени. С определенной даты и по текущий момент
     * */
    public function getLessonsByClassIdAndTime($class_id_mk, $timestart=0)
    {
        return DB::getAll('SELECT `l`.`lesson_id_mk`,`rl`.`user_id_mk`,`l`.`timestart`,`l`.`class_id_mk` FROM `lessons` `l`
                            INNER JOIN `recordslesson` `rl` ON `rl`.`lesson_id_mk` = `l`.`lesson_id_mk` 
                            WHERE `l`.`class_id_mk`=:class_id_mk 
                            && `l`.`timestart`>=:timestart && `l`.`timestart`<=:timeend
                            GROUP BY `user_id_mk`
                            ORDER BY `l`.`timestart` DESC', [
                                'class_id_mk' => $class_id_mk,
                                'timestart' => $timestart, // && `rl`.`visit`=1
                                'timeend' => time(),
        ]);
    }

    public function getOpenDialogsTeacher($manager_id){
        return DB::getAll('SELECT * FROM `chatdialogs` WHERE `manager_id_mk`=:manager_id', [
            'manager_id' => $manager_id
        ]);
    }

    public function getContactsTeacher($manager_id){



        $getClasses = $this->getClasses($manager_id);

        $manager_member = DB::getRowByKey('member', 'mk_manager_id', $manager_id, ['id', 'first_name', 'last_name', 'mk_uid', 'foto_url']);

        $students = [];

        /*var_dump($this->getLessonsByClassIdAndTime(180508, 1648145800));
        return;*/


        foreach ($getClasses as $getClass) {

            // Ограничение по времени, которое сохраняет связку чата с клиентом
            $timestart = time();
            if($getClass['individual']==true){
                // Для индивидуального занятия сохраняем связку 1 год
                $timestart = $timestart - (60 * 60 * 24 * 365);
            }else{
                // Для группового занятия сохраняем связку 6 недель (45 дней)
                $timestart = $timestart - (60 * 60 * 24 * 45);
            }
            //$timestart = 0; // убрать
            $lessons = $this->getLessonsByClassIdAndTime($getClass['group_id_mk'], $timestart);
            //var_dump($getClass['group_id_mk']);
            //var_dump($timestart);
            foreach ($lessons as $lesson) {




                $student = $lesson['user_id_mk'];
                $students[$student]['client_id'] = $lesson['user_id_mk'];

                $students[$student]['manager_member'] = $manager_member;//$Member->getMemberByMkUid($student);
                $client_member = DB::getRowByKey('member', 'mk_uid', $student, ['first_name', 'last_name', 'mk_uid', 'foto_url']);//$Member->getMemberByMkUid($student);
                if(empty($client_member)){
                    unset($students[$student]);
                    continue; // Если ученика нет в системе, пропускаем его
                }



                $students[$student]['client_member'] = $client_member;//$Member->getMemberByMkUid($student);

                $students[$student]['dialog_name'] = $manager_member['last_name'] . ' ' .$manager_member['first_name']
                    . ' - ' .$client_member['last_name'] . ' ' . $client_member['first_name'];

                $datagroup['groupsync'] = $getClass;
                $datagroup['class'] = DB::getRowByKey('class', 'id', $getClass['class_id'], ['name']);//$GroupsModel->getClassById($getClass['class_id'])[0];
                $datagroup['program'] = DB::getRowByKey('program', 'id', $getClass['program_id'], ['name']); // $GroupsModel->getProgramById($getClass['program_id'])[0];
                $getDialog = $this->getFindDialog($manager_member['id'], $students[$student]['client_member']['id']); // Находим диалог, если нет - создаем
                $students[$student]['dialog_id'] = $getDialog['id'];
                $students[$student]['banned'] = $getDialog['banned'];
                $students[$student]['calladmin'] = $getDialog['calladmin'];
                $students[$student]['timestart'] = $lesson['timestart'];
                $students[$student]['count_unread_messages'] = DB::count('chatmessages', 'WHERE `read`<>1 && `chatdialog_id`=:chatdialog_id && `from_member_id`<>:manager_member_id', ['chatdialog_id' => $getDialog['id'], 'manager_member_id' => $manager_member['id']]);
                $students[$student]['lastmessage_time'] = DB::getRowByKey('chatmessages', 'chatdialog_id', $getDialog['id'], ['time'], 'ORDER BY `id` DESC LIMIT 1')['time'];

                $students[$student]['groups'][] = $datagroup;

            }
            uasort($students, '\GKTOMK\Models\ChatModels\ChatTeacherModel::cmp_desc_timestart');
            uasort($students, '\GKTOMK\Models\ChatModels\ChatTeacherModel::cmp_desc_lastmessage');
            //uasort($students, '\GKTOMK\Models\ChatModels\ChatTeacherModel::cmp_desc_lastmessage');

        }
        $this->setDialogsGroups($students);
        $students = array_values($students);


        return $students;
    }

    public function getTeacherDialogsOpenByManagerMemberId($manager_member_id, $date_update=0){
        $dialogs = $this->getDialogsByManagerMemberId($manager_member_id);
        $output = [];
        foreach ($dialogs as $dialog) {
            $output[$dialog['id']] = $this->getDialogInfo($dialog['id']);
            if($output[$dialog['id']]['date_update_manager']<=$date_update){
                unset($output[$dialog['id']]);
            }
        }
        return $output;
    }

    public function setCallAdminByDialogId($dialog_id){
        $this->setDialog($dialog_id, 'calladmin', 1);
        return $this->setDialog($dialog_id, 'date_update_admin', time());
    }

    public function setUnCallAdminByDialogId($dialog_id){
        $this->setDialog($dialog_id, 'calladmin', 0);
        return $this->setDialog($dialog_id, 'date_update_manager', time());
    }

    function cmp_desc_timestart($a, $b){
        if($a['timestart'] and $b['timestart'])
            return ($a['timestart'] < $b['timestart']);
        else
            return false;
    }

    function cmp_desc_lastmessage($a, $b){
        if($a['lastmessage_time']==$b['lastmessage_time'])
            return false;
        return ($a['lastmessage_time'] < $b['lastmessage_time']);
    }

}