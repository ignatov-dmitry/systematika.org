<?php


namespace GKTOMK\Models;


class ScheduleModel
{

    /*
     * Отдает подготовленный список расписания для выдачи в шаблон
     * */
    public function getSchedule($email = 'test@gk.ru')
    {
        // Для теста.
        // ИД Занятия: 6262648 Юзер: test@gk.ru
        $user = MoyklassModel::getUserByEmail($email);

        // print_r($user);

        if (empty($user['id']))
            return [];

        $month_now_first_day = date('Y-m-d');
        $month_now_last_day = date('Y-m-d', (time() + (60 * 60 * 24 * 14)));
        $mnths = array("1" => "января", "2" => "февраля", "3" => "марта", "4" => "апреля", "5" => "мая", "6" => "июня", "7" => "июля", "8" => "августа", "9" => "сентября", "10" => "октября", "11" => "ноября", "12" => "декабря");
        $weekdays = array("1" => "пн", "2" => "вт", "3" => "ср", "4" => "чт", "5" => "пт", "6" => "сб", "7" => "вс");

        $lessons = MoyklassModel::getLessons([
            'userId' => $user['id'],
            'date[0]' => $month_now_first_day,
            'date[1]' => $month_now_last_day
        ]);


        usort($lessons['lessons'], "\GKTOMK\Models\ScheduleModel::cmp");

        $dataList = [];
        foreach ($lessons['lessons'] as $lesson) {
            $ind++;
            $lesson['index'] = $ind;
            $lesson['timestart'] = strtotime($lesson['date'] . ' ' . $lesson['beginTime']);

            if (($lesson['timestart'] + (60 * 60 * 2)) < time()) // Не показываем уроки спустя 2 часа после их начала
                continue;

            $lesson['daynumber'] = date("d", strtotime($lesson['date']));
            $lesson['dayofweek'] = date("D", strtotime($lesson['date']));

            $lesson['monthtxt'] = $mnths[date("n", strtotime($lesson['date']))];
            $lesson['weekday'] = $weekdays[date("N", strtotime($lesson['date']))];

            $lesson['CLASS'] = MoyklassModel::getClassById($lesson['classId']);
            $lesson['COURSE'] = MoyklassModel::getCourseById($lesson['CLASS']['courseId']);

            $lesson['url'] = preg_replace('!(https|ftp|scp)(s)?:\/\/[a-zA-Z0-9.?&_/]+!', "\\0", $lesson['topic']);;
            $lesson['topic'] = preg_replace('!(https|ftp|scp)(s)?:\/\/[a-zA-Z0-9.?&_/]+!', "<a href=\"\\0\" class=\"monthly-list-time-start\" target='_blank'>\\0</a>", $lesson['topic']);;

            $dataList[] = $lesson;
        }
        // print_r($dataList);

        //$classes = MoyklassModel::getClasses();

        // print_r($classes);


        // $courses = MoyklassModel::getCourses();

        // print_r($courses);

        // print_r($dataList);
        return $dataList;
    }

    /*
    * Сортировка массива по дате, затем по времени
    * */
    function cmp($a, $b)
    {
        // extract year, month and day from date
        list($a_month, $a_day, $a_year) = explode('/', $a['date']);
        list($b_month, $b_day, $b_year) = explode('/', $b['date']);
        // compare the correctly ordered strings
        return strcmp($a_year . $a_month . $a_day . $a['beginTime'] . $a['endTime'],
            $b_year . $b_month . $b_day . $b['beginTime'] . $b['endTime']);
    }

}