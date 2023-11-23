<?php


namespace GKTOMK\Models;


class StatisticsModel
{

    public function __construct()
    {
        DB::init();
    }

    public function getLoadVisits($dateStart, $dateFinish)
    {
        $lessons = MoyklassModel::getLessons(['date' => [$dateStart, $dateFinish], 'includeRecords' => 'true'/*, 'limit' => '10'*/]);

        foreach ($lessons['lessons'] as $lesson) {
            //var_dump($lesson);


           /* echo '<br/> Занятие: ';
            echo $lesson['id'] . '<br/> Дата: ';
            echo $lesson['date'] . '<br/> Группа: ';
            echo $lesson['classId'] . '<br/> Записано: ';
            echo count($lesson['records']) . '<br/> Посетило:';
            echo $this->getCountRecordsVisit($lesson['records']) . '<br/>';
            echo '<br/>';
            echo '<br/>';*/


            $this->saveVisitsLesson([
                'lessonId' => $lesson['id'],
                'classId' => $lesson['classId'],
                'date' => $lesson['date'],
                'beginTime' => $lesson['beginTime'],
                'endTime' => $lesson['endTime'],
                'numrecords' => count($lesson['records']),
                'numvisits' => $this->getCountRecordsVisit($lesson['records']),
            ]);

            usleep(100);
           // return;
        }

    }

    private function getCountRecordsVisit($records = [])
    {
        $num = 0;
        foreach ($records as $record) {
            if ($record['visit'] == true) {
                $num++;
            }
        }
        return $num;
    }

    private function setSaveVisitsLesson($data = [])
    {
        if (!isset($data['id'])) {
            $visitslesson = DB::dispense('visitslesson');
        } else {
            $visitslesson = DB::load('visitslesson', $data['id']);
        }

        $visitslesson->lessonId = $data['lessonId'];
        $visitslesson->classId = $data['classId'];
        $visitslesson->date = $data['date'];
        $visitslesson->beginTime = $data['beginTime'];
        $visitslesson->endTime = $data['endTime'];
        $visitslesson->numRecords = $data['numrecords'];
        $visitslesson->numVisits = $data['numvisits'];
        DB::store($visitslesson);
    }

    /**
     * Сохраняет посещения для конкретного занятия
     * */
    private function saveVisitsLesson($data = [])
    {

        $lesson = $this->getVisitsLessonByLessonId($data['lessonId']);

        if (isset($lesson['id'])) {
            $data['id'] = $lesson['id'];
        }

        $this->setSaveVisitsLesson($data);
    }

    private function getVisitsLessonByLessonId($lessonId)
    {
        return DB::getRow('SELECT `id` FROM `visitslesson` WHERE `lesson_id`=? LIMIT 1', [$lessonId]);
    }

    public function getVisitsLessonByClassId($classId)
    {
        $dateToday = date("Y-m-d");
        $stats = DB::getAll('SELECT `date`,`begin_time`,`num_records`,`num_visits` FROM `visitslesson` WHERE `class_id`=:class && `date`<:today ORDER by `date` DESC LIMIT 3', [
            'class' => $classId,
            'today' => $dateToday
            ]);

        $futureStat = DB::getRow('SELECT `date`,`begin_time`,`num_records` FROM `visitslesson` WHERE `class_id`=:class && `date`>=:today ORDER by `date` ASC LIMIT 1', [
            'class' => $classId,
            'today' => $dateToday
        ]);

        array_unshift($stats, $futureStat);

        return $stats;
    }

    public function getStstistics(){

    }

}