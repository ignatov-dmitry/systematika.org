<?php
// Class DB NEW. Класс для установки подключения с бд через редбинпхп

namespace App\Lib;

use \App\Config;
use \RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class DBN extends R
{

    public function __construct()
    {
        self::init();
    }

    public static function init()
    {

        if (!defined('CONFIG')) Config::init();

        try {
            R::addDatabase('DB', 'mysql:host=' . CONFIG['mysql_credentials']['host'] . ';dbname=' . CONFIG['mysql_credentials']['database'],
                CONFIG['mysql_credentials']['user'], CONFIG['mysql_credentials']['password'], false);
        } catch (RedException $e) {
            return $e;
        }

        try {
            R::selectDatabase('DB');
        } catch (RedException $e) {
            return $e;
        }
    }
}