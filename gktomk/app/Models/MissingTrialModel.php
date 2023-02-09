<?php
/*
 * Модель для работы с пропусками пробных занятий
 * */

namespace GKTOMK\Models;


class MissingTrialModel
{
    public function __construct()
    {
        DB::init();
    }

    /*
     * Cоздает запись в БД
     * */

    public function addMissing($lesson_id)
    {

        $missing = $this->getMissingByLesson($lesson_id);

        // var_dump($missing);

        // Если такого пропуска нет или он уже обработан, добавляем его
        if (!isset($missing) or !isset($missing['id']) or (($missing['date_create'] + 60 * 60) < time()) or $missing['status'] == 1) {
            $this->createMissing($lesson_id);
        }
    }

    /*
     * Добавляет занятие на проверку, если его нет за последний час
     * */

    public function getMissingByLesson($lesson_id)
    {
        return DB::getRow('SELECT * FROM `missing` WHERE `mk_lesson`=:lesson LIMIT 1', [
            'lesson' => $lesson_id
        ]);
    }

    public function createMissing($lesson_id)
    {
        $missing = DB::dispense('missing');
        $missing->mk_lesson = $lesson_id;
        $missing->status = 0;
        $missing->date_create = time();
        DB::store($missing);
    }

    /**
     * Обработать все пропуски
     * */
    public function handleMissings()
    {

        $missings = $this->getMissingsForHandle();

        foreach ($missings as $missing) {
            $this->updateMissingLesson($missing['mk_lesson']);
            $this->setStatusMissingSuccess($missing['id']);
        }

    }

    /**
     * Получить пропуски готовые к обработке
     * */

    public function getMissingsForHandle()
    {
        $timeFilter = (time() - 60 * 60);
        return DB::getAll('SELECT * FROM `missing` WHERE `date_create`<=:time && `status`=0', [
            'time' => $timeFilter
        ]);
    }

    public function updateMissingLesson($lesson_id)
    {
        $lesson = MoyklassModel::getLessonById($lesson_id, ['includeRecords' => 'true']);
        var_dump($lesson);
        foreach ($lesson['records'] as $record) {


            if ($record['test'] == false or $record['visit'] == true)
                continue;

            $userMk = MoyklassModel::getUserById(['userId' => $record['userId']]);

            if (!isset($userMk) or !isset($userMk['id']))
                continue;

            $GetCourse = new GetcourseModel();
            $GetCourse->init([
                'email' => $userMk['email'],
                'fields' => [
                    CONFIG['gk_field_date_missing_free_test'] => date("d.m.Y", strtotime($lesson['date']))
                ]
            ])->send();

            // var_dump($userMk);

        }


    }

    /*
     * Обновляет пропуски для занятия
     * */

    public function setStatusMissingSuccess($missing_id)
    {
        $missing = DB::load('missing', $missing_id);
        $missing->status = 1;
        DB::store($missing);
    }

}