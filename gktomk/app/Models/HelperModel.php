<?php


namespace GKTOMK\Models;


class HelperModel
{

    public static function timeleft($datetime,
                                    $options =
                                    ['days' => ['день', 'дня', 'дней'],
                                        'hours' => ['час','часа','часов'],
                                        'minutes' => ['минута','минуты','минут']
                                    ]
    ){

        $check_time = $datetime - time();
        if($check_time <= 0){
            return false;
        }

        $days = floor($check_time/86400);
        $hours = floor(($check_time%86400)/3600);
        $minutes = floor(($check_time%3600)/60);
        $seconds = $check_time%60;

        $str = '';
        if($days > 0  and isset($options['days'])) $str .= self::declension($days,$options['days']).' ';
        if($hours > 0  and isset($options['hours']))  $str .= self::declension($hours,$options['hours']).' ';
        if($minutes > 0 and isset($options['minutes'])) $str .= self::declension($minutes,$options['minutes']).' ';
        if($seconds > 0  and isset($options['seconds'])) $str .= self::declension($seconds,$options['seconds']);

        return $str;
    }

    public static function declension($digit,$expr,$onlyword=false){
        if(!is_array($expr)) $expr = array_filter(explode(' ', $expr));
        if(empty($expr[2])) $expr[2]=$expr[1];
        $i=preg_replace('/[^0-9]+/s','',$digit)%100;
        if($onlyword) $digit='';
        if($i>=5 && $i<=20) $res=$digit.' '.$expr[2];
        else
        {
            $i%=10;
            if($i==1) $res=$digit.' '.$expr[0];
            elseif($i>=2 && $i<=4) $res=$digit.' '.$expr[1];
            else $res=$digit.' '.$expr[2];
        }
        return trim($res);
    }

    public static function onlyNumbers($number){
        return preg_replace("/[^0-9]/", '', $number);
    }


}