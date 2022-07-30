<?php
/*
 * Модель работает с добавлением пользователей в группы
 * */

namespace GKTOMK\Models;


class AddclassModel
{

    public function getClasses($userId)
    {
    }


    public function addClass($leadId, $classId)
    {


        $LeadsModel = new LeadsModel();
        $lead = $LeadsModel->getUserById($leadId);



        $mk_user = $LeadsModel->getFindUserByEmail($lead['gk_email']);

        $this->deleteStartGroup($mk_user['joins']);


       // print_r($mk_user);


        $Sync = new SyncModel();
        $sync = $Sync->getSync(['gk_offer' => $lead['gk_offers']]);

        //var_dump($mk_user);



       // return;


        $statusId = 2; // Статус "Учится"
        $autoJoin = true;

        /// Случай, если у нас пробное занятие
        if($sync[0]['demo']==1){ // Пробное занятие
            echo 'DEMO';
            $statusId = CONFIG['statusGroup']['recorded']; // Статус "Записан" - id 31034
            $autoJoin = false; // Не записываем на все занятия
            $res = $this->add($mk_user['id'], $classId, $statusId, $autoJoin);
           // var_dump($res);

        }elseif(count($mk_user['joins']) == 0 or (!empty($mk_user['joins'][0]) and $mk_user['joins'][0]['classId'] == CONFIG['startGroup'])){ // Случай, если групп у пользователя вообще нет
            $statusId = CONFIG['statusGroup']['learns']; // Статус "Учится"
            $autoJoin = true; // Записываем на все занятия
            $res = $this->add($mk_user['id'], $classId, $statusId, $autoJoin);
           // var_dump($res);
        }

/*
        elseif ($this->checkGroupStatusRecordedByJoins($mk_user['joins'], CONFIG['statusGroup']['recorded'])){ // Случай, когда есть запись в группу со статусом записан

        }*/

        //$r = MoyklassModel::getJoins(['userId' => $mk_user['id'], 'classId' => $classId]);
        //print_r($r);


       // $lessonId = $this->getFirstLesson($classId);
       // echo 'Занятие такое-то: ' . $lessonId;

        //$res = $this->addRecordTest($mk_user['id'], $lessonId);
       // var_dump($res);


        //$recordId = $this->getFirstRecordLesson($lessonId, $mk_user['id']);
        //var_dump($recordId);
        //$result = MoyklassModel::setJoins(['userId' => $mk_user['id'], 'classId' => intval($classId), 'statusId' => $statusId, 'autoJoin' => $autoJoin]);

        return 1;
    }

    /* Метод добавляет пользователя в группу
     *
     *
     * */
    public function add($userId, $classId, $statusId, $autoJoin){
        $result = MoyklassModel::setJoins(['userId' => $userId, 'classId' => intval($classId), 'statusId' => intval($statusId), 'autoJoin' => $autoJoin]);
        return $result;
    }

    /* Метод добавляет пользователю 1 запись на занятие
     *
     * */
    private function addRecordTest($userId, $lessonId){
        $res = MoyklassModel::setLessonRecords(['userId' => $userId, 'lessonId' => $lessonId, 'free' => true]);
        return $res;
    }



    public function getFirstLesson($classId){
        $lessons = MoyklassModel::getLessons(['classId' => $classId]);
        $startDate = strtotime(0);
        foreach($lessons['lessons'] as $lesson){
            $date = strtotime($lesson['date']);
            if($startDate < $date){
                $lessonId = $lesson['id'];
                $startDate = $date;
            }
        }
        return $lessonId;
    }

    private function getFirstRecordLesson($lessonId, $userId){
        $lesson = MoyklassModel::getLessonById($lessonId, ['includeRecords' => 'true']);
        var_dump($lesson['records']);
        $recordId = 0;
        foreach ($lesson['records'] as $record){
            if($record['userId'] == $userId){
                $recordId = $record['id'];
                break;
            }
        }
        return $recordId;
    }


    private function deleteStartGroup($joins){
        if (!empty(CONFIG['startGroup']) and CONFIG['startGroup'] > 0 and CONFIG['startGroup_delete']) {
            $start_join = 0;
            foreach ($joins as $join) {
                if ($join['classId'] == CONFIG['startGroup']) {
                    $start_join = $join['id'];
                }
            }
            if ($start_join > 0) {
                // Удаляем из стартовой группы
                MoyklassModel::deleteJoins(['joinId' => $start_join]);
            }
        }
    }




}