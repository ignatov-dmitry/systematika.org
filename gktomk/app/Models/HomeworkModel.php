<?php
// Модель для работы с лидами. Сохраняет и получает лиды

namespace GKTOMK\Models;

class HomeworkModel
{
    public function __construct()
    {
        DB::init();
    }

    /*Создает заявку на открытие домашнего задания*/

    public function createHwk($data)
    {
        $homework = DB::dispense('homework');

        $homework->mk_lessonRecordId = $data['lessonRecordId'];
        $homework->mk_userId = $data['userId'];
        $homework->mk_lessonId = $data['lessonId'];
        $homework->visit = $data['visit'];
        $homework->test = $data['test'];
        $homework->date_add = time();
        $homework->date_update = time();
        $homework->status = 'new';
        return DB::store($homework);
    }

    public function getAllHwk()
    {
        return DB::exportAll(DB::findAll('homework', 'ORDER BY `id` DESC'));
    }




    /*
     * [CRON] Отправляет пользователей в МойКласс
     * */

    public function getCronHwk()
    {
        return DB::findAll('homework', 'status = ?', ['new']);
    }


    public function getHwkById($hwkId)
    {
        return DB::load('homework', $hwkId)->export();
    }


    public function setHwk($hwk_id, $data = [])
    {
        $user = DB::load('homework', $hwk_id);
        foreach ($data as $key => $value) {
            $user->{$key} = $value;
        }
        return DB::store($user);
    }

    /**
     * Функция добавления логов для юзера
     * */
    public function addLogHwk($hwkId, $code, $text = '', $debug = '')
    {
        $user = DB::load('homework', $hwkId);
        $log = DB::dispense('loghwk');
        $log->code = $code;
        $log->text = $text;
        $log->debug = $debug;
        // Добавляем связь с домашкой
        $user->ownLogList[] = $log;
        DB::store($user);
    }

    /**
     * Загрузка логов конкретного юзера
     * */
    public function getLogHwk($hwkId)
    {
        $user = DB::load('homework', $hwkId);
        return DB::exportAll($user->ownLog);
    }

    /**
     * Определяет какая группа указана в сообщении
     * */
    public function findGroup($desc){
        preg_match('@\[([^[]*)\]@', $desc, $matches);
        if(isset($matches[1]))
            return '['.@$matches[1].']';
        else
            return 0;
    }


    /*
     * Отдает список уникальных пользователей
     * */
    public function getUnicUsers(){
        return DB::getAll('SELECT `mk_user_id` FROM `homework` GROUP by `mk_user_id`');
    }
}