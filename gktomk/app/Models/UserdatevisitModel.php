<?php


namespace GKTOMK\Models;


use GKTOMK\Models\GetCourse\core\Model;

class UserdatevisitModel extends Model
{

/*    public static function getUserDateVisitByMkUid($mk_id){

        // Обновляем дату последнего пробного
        $lesson_last_test = MoyklassModel::getLessonVisitLastTest($mk_id);
        if (isset($lesson_last_test) and !empty($lesson_last_test)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last_test['date']));
            $DataUser['date_last_test_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $DataUser['date_last_test_lesson'] = '01.01.1970';
        }

        // Дата последнего посещения урока
        $lesson_last = MoyklassModel::getLessonVisitLast($mk_id);
        if (isset($lesson_last) and !empty($lesson_last)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last['date']));
            $DataUser['date_last_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $DataUser['date_last_lesson'] = '01.01.1970';
        }
        return $DataUser;
    }*/

    public static function getUserDateVisitByMkUid($mk_id){

        // Обновляем дату последнего пробного
        $lesson_last = MoyklassModel::getLessonVisitLastByUserId($mk_id);

        if (!empty($lesson_last['date_last_test_lesson'])) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last['date_last_test_lesson']['date']));
            $DataUser['date_last_test_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $DataUser['date_last_test_lesson'] = '01.01.1970';
        }

        // Дата последнего посещения урока
        if (!empty($lesson_last['date_last_lesson'])) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last['date_last_lesson']['date']));
            $DataUser['date_last_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $DataUser['date_last_lesson'] = '01.01.1970';
        }
        return $DataUser;
    }

    public static function getUserDateVisitByEmail($email){

        $userMk = MoyklassModel::getUserByEmail($email);

        //var_dump($userMk);

        if(!isset($userMk) or !isset($userMk['email']))
            return 'mk user not found';


        return self::getUserDateVisitByMkUid($userMk['id']);
    }

}