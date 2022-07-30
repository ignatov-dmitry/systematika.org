<?php
// Class DB NEW. Класс для установки подключения с бд через редбинпхп

namespace GKTOMK\Models;

use \GKTOMK\Config;
use \RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class DB extends R
{

    public function __construct()
    {
        self::init();
    }

    public static function init(){

        if(!defined('CONFIG')) Config::init();

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

    /** Редактирует или создает любую запись в любой таблице
     *
     * */
    public static function edit($table, $data = [])
    {
        //var_dump($data);
        if(empty($data['id']) or $data['id'] == null){
            $row = DB::dispense($table);
        }else{
            $row = DB::load($table, $data['id']);
        }
        unset($data['id']);
        foreach ($data as $key => $value) {
            $row->{$key} = $value;
        }
        return DB::store($row);
    }

    public static function delete($table, $key, $value){
        return self::exec("DELETE FROM `{$table}` WHERE `{$key}`='{$value}'");
    }

    public static function getRowByKey($table, $key, $value, $selected = [], $order = '')
    {

        if(empty($selected) or !is_array($selected))
            $selected = [$key];

        $keys = '`id`';
        foreach ($selected as $item) {
            if($item == '*'){
                $keys = '*';
                break;
            }

            $keys .= ",`{$item}`";
        }

        return self::getRow("SELECT {$keys} FROM `{$table}` WHERE `{$key}`=:value " . $order, ['value' => $value]);
    }

    public static function getAllByKey($table, $key, $value, $selected = [])
    {

        if(empty($selected) or !is_array($selected))
            $selected = [$key];

        $keys = '`id`';
        foreach ($selected as $item) {
            if($item == '*'){
                $keys = '*';
                break;
            }

            $keys .= ",`{$item}`";
        }

        return self::getAll("SELECT {$keys} FROM `{$table}` WHERE `{$key}`=:value", ['value' => $value]);
    }

    public static function getOption($table, $name)
    {
        return self::getRowByKey($table, 'name', $name, ['id', 'value'])['value'];
    }

    public static function setOption($table, $name, $value)
    {
        $get = self::getRowByKey($table, 'name', $name, ['id', 'value']);
        return self::edit($table, ['id' => $get['id'], 'name' => $name, 'value' => $value]);
    }

    public static function deleteOption(){

    }
}