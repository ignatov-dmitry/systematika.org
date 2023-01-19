<?php


namespace GKTOMK\Models;


class AddgroupModel
{

    public function addJoinGroup($userId, $classId, $statusId, $autoJoin)
    {
        return MoyklassModel::setJoins(['userId' => intval($userId), 'classId' => intval($classId), 'statusId' => intval($statusId), 'autoJoin' => $autoJoin]);
    }


    /*
     * Редактирует или добавляет заявку
     * */
    public function editJoinGroupByClassId($userId, $classId, $statusId=0, $autoJoin=0)
    {
        // statusId = 31034 - Записан
        $getJoins = MoyklassModel::getJoins(['userId' => intval($userId), 'classId' => intval($classId)]); // ,
        //print_r($getJoins);
        if(empty($getJoins['joins'])){
            return $this->addJoinGroup($userId, $classId, $statusId, $autoJoin);
        }else{
            $joinData = [];
            foreach ($getJoins['joins'] as $join) {
                if($join['userId']==$userId and $join['classId']==$classId){
                    $joinData = $join;
                }
            }
            if(empty($joinData)){
                return 'notfoundjoin';
            }
            //print_r($joinData);

            $param = [
                'price' => 0,
                'statusId' => intval('31034'),
                'statusChangeReasonId' => $joinData['statusChangeReasonId'],
                'autoJoin' => $autoJoin,
                'comment' => $joinData['comment'],
                'advSourceId' => $joinData['advSourceId'],
                'createSourceId' => $joinData['createSourceId']
            ];
            return MoyklassModel::editJoins($joinData['id'], $param);
        }


    }

    public function addRecordLesson($userId, $lessonId, $test = true)
    {
        return MoyklassModel::setLessonRecords(['userId' => intval($userId), 'lessonId' => intval($lessonId), 'test' => boolval($test), 'free' => boolval($test)]);
    }

    public function deleteRecordLessonByLessonId($userId, $lessonId)
    {

    }

    /*
     * Записывает на все будущие уроки в группе, с возможностью исключения
     * */
    public function addRecordAllLessonByClassId($userId, $classId, $excludeLessons = [])
    {
        $firstTestLesson = true;
        $month_now_first_day = date('Y-m-d', time());
        $month_now_last_day = date('Y-m-d', (time() + (60 * 60 * 24 * 180))); // записываем на полгода

        $lessons = [];
        $lessons = MoyklassModel::getLessons([
            'classId' => intval($classId),
            'date[0]' => $month_now_first_day,
            'date[1]' => $month_now_last_day,
        ]);

        if(count($lessons)<1)
            return 'Lessons not found';

        $result = '';

        foreach ($lessons['lessons'] as $lesson) {
            if(in_array($lesson['id'], $excludeLessons))
                continue;

            $result = $this->addRecordLesson($userId, $lesson['id'], $firstTestLesson);
            $firstTestLesson = false;
        }
        return $result;

    }


}
