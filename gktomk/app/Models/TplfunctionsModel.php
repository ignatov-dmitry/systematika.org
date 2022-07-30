<?php


namespace GKTOMK\Models;


class TplfunctionsModel
{

    public static function timeFormat($time) { // преобразовываем время в нормальный вид
        date_default_timezone_set('Europe/Moscow');
        $ndate = date('d.m.Y', $time);
        $ndate_time = date('H:i', $time);
        $ndate_exp = explode('.', $ndate);
        $nmonth = array(
            1 => 'янв',
            2 => 'фев',
            3 => 'мар',
            4 => 'апр',
            5 => 'мая',
            6 => 'июн',
            7 => 'июл',
            8 => 'авг',
            9 => 'сен',
            10 => 'окт',
            11 => 'ноя',
            12 => 'дек'
        );

        foreach ($nmonth as $key => $value) {
            if($key == intval($ndate_exp[1])) $nmonth_name = $value;
        }

        if($ndate == date('d.m.Y')) return 'сегодня в '.$ndate_time;
        elseif($ndate == date('d.m.Y', strtotime('-1 day'))) return 'вчера в '.$ndate_time;
        else return $ndate_exp[0].' '.$nmonth_name.' '.$ndate_exp[2].' в '.$ndate_time;
    }

}