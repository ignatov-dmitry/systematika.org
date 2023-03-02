<?php
// Class DB NEW. Класс для установки подключения с бд через редбинпхп

namespace GKTOMK\Models;

use ArrayAccess;
use \GKTOMK\Config;
use GKTOMK\Models\Systematika\Util;
use InvalidArgumentException;
use LogicException;
use \RedBeanPHP\R as R;
use RedBeanPHP\RedException;

class DB extends R
{
    private static $queryCount = 0;

    private static $insertCount = 0;

    const OP_EXPR     = 'expr';
    const OP_EQUAL    = '=';
    const OP_NOTEQUAL = '!=';
    const OP_AND      = 'AND';
    const OP_OR       = 'OR';
    const OP_NOT      = 'NOT';
    const OP_IN       = 'IN';
    const OP_NOTIN    = 'NOT IN';
    const OP_IS       = 'IS';
    const OP_ISNOT    = 'IS NOT';
    const OP_LT       = '<';
    const OP_GT       = '>';
    const OP_LTE      = '<=';
    const OP_GTE      = '>=';
    const OP_BETWEEN  = 'BETWEEN';
    const OP_LIKE     = 'LIKE';
    const OP_NOTLIKE  = 'NOT LIKE';

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

    public static function getRowByKey($table, $key, $value, $selected = [])
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

        return self::getRow("SELECT {$keys} FROM `{$table}` WHERE `{$key}`=:value", ['value' => $value]);
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

    public static function getColumnValues($array = [], $columns = [])
    {
        return array_map(function ($item) use ($columns){
            $cleanItem = [];
            foreach ($columns as $column)
                $cleanItem[$column] = $item[$column];

            return $cleanItem;

        }, $array);
    }

    public static function addFields(&$array, $data = [])
    {
        foreach ($data as $key => $value)
            $array[$key] = $value;
    }


    //SQL BUILDER

    public final function prepareInsert($table, array $data, $ignore = false) {
        return $this->prepareBulkInsert($table, array_keys($data), array(array_values($data)), $ignore);
    }

    public final function prepareWhere(array $criteria, $logicOp = self::OP_AND) {
        $op = ' ' . $logicOp . ' ';

        return implode($op, $this->prepareCriteria($criteria));
    }

    public function Quote($var = '') {
        if (is_string($var) || is_numeric($var) || is_null($var)) {
            return $this->MysqlEscape(stripslashes($var));
        }
        else if (is_array($var)) {
            return array_map(array($this, 'Quote'), $var);
        }
        else if (is_bool($var)) {
            return (int) $var;
        }
        else {
            trigger_error("Invalid type passed to DB quote " . gettype($var), E_USER_ERROR);

            return false;
        }
    }

    public function MysqlEscape($var) {
        return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $var);
        //return mysqli_real_escape_string(DB::getDatabaseAdapter()->getDatabase()->getPDO()->, $var);
    }

    protected final function prepareVal($val) {
        switch (true) {
            case is_null($val):
                $val = 'NULL';
                break;
            case is_bool($val):
                $val = $val ? 'TRUE' : 'FALSE';
                break;
            case is_int($val) || is_float($val):
                // note that `is_numeric()` won't work, as it will catch strings like "0xFF"
                // numbers are already good enough, proceed!
                break;
            case is_string($val):
                $val = $this->Quote($val);
                $val = self::unquote($val);
                break;
            case is_array($val):
                foreach ($val as &$v) $v = $this->prepareVal($v);
                $val = implode(', ', $val);
                $val = self::wrap($val);
                break;
            case is_object($val): // object check should go after everything else as the least specific
                $val = $this->prepareVal((string) $val);
                break;
            default:
                $errMsg = 'Illegal value type (%s)';
                throw new InvalidArgumentException(sprintf($errMsg, gettype($val)));
        }

        return $val;
    }

    protected final function prepareKeyValOp($key, $val, $op = self::OP_EQUAL, $set = false) {
        $cmp = '%s %s %s'; // default

        switch (true) {
            case $op === self::OP_BETWEEN && is_array($val):
                foreach ($val as &$v) $v = $this->prepareVal($v);
                $val = implode(' '.self::OP_AND.' ', $val);
                break;
            case is_null($val):
                if (!$set) {
                    if ($op == self::OP_EQUAL)    $op = self::OP_IS;
                    if ($op == self::OP_NOTEQUAL) $op = self::OP_IS . ' ' . self::OP_NOT;
                }
                $val = $this->prepareVal($val);
                break;
            case is_bool($val):
                $val = $this->prepareVal($val);
                break;
            case is_int($val) || is_float($val):
                $val = $this->prepareVal($val);
                break;
            case is_array($val):
                if ($op == self::OP_EQUAL) $op = self::OP_IN;
                if ($op == self::OP_NOTEQUAL) $op = self::OP_NOTIN;

                if (!in_array($op, array(self::OP_IN, self::OP_NOTIN))) {
                    throw new LogicException('Array type criteria can only associate with IN or NOT IN. Your provided operator is ' . $op);
                }

                if (!$val) return ($op == self::OP_IN) ? 'FALSE' : 'TRUE';
                $val = $this->prepareVal($val);
                break;
            default:
                $val = $this->prepareVal($val);
        }

        return sprintf($cmp, $key, $op, $val);
    }

    protected final function prepareCriteria(array $criteria, $defaultOp = self::OP_EQUAL, $set = false) {
        $prepared = array();

        foreach ($criteria as $key => $val) {
            // If $val is an array with another `val` key inside, then consider it as a complex criteria.
            if (is_array($val) && array_key_exists('val', $val)) {
                $op  = $this->ifx($val, 'op', $defaultOp);
                // self::notEqualCond() return key with null value, we need to add additional check when the key exists but the key value is null
                $key = $this->ifx($val, 'key') ?: $key;
                $val = $val['val'];
            }
            else $op = $defaultOp;

            $prepared[] = $this->prepareKeyValOp($key, $val, $op, $set);
        }

        return $prepared;
    }

    public final function prepareBulkInsert($table, array $keys, array $data, $ignore = false) {
        $valuesList =  array();

        foreach ($data as $values) {
            foreach ($values as &$value) {
                $value = $this->prepareVal($value);
            }

            $valuesList[] = self::wrap(implode(', ', $values));
        }

        $sql = 'INSERT {ignore} INTO {tn} ({keys}) VALUES {vals}';
        $sql = Util::replaceTokens($sql, array(
            'ignore' => $ignore ? 'IGNORE' : '',
            'tn'     => $table,
            'keys'   => implode(', ', $keys),
            'vals'   => implode(', ', $valuesList)
        ));

        return $sql;
    }

    final public static function unquote($sql) {
        return sprintf('"%s"', trim($sql));
    }

    final public static function wrap($sql) {
        return sprintf('(%s)', trim($sql));
    }

    function ifx($array, $key, $else = null) {
        if (is_array($array) && array_key_exists($key, $array)) {
            return $array[$key];
        }
        else if ($array instanceof ArrayAccess && isset($array[$key])) {
            return $array[$key];
        }
        else if (is_object($array) && property_exists($array, $key)) {
            return $array->$key;
        }

        return $else;
    }

    public function getTableColumn($table)
    {
        $tableSchema = self::getAll('SELECT DATABASE()');

        $condition = array(
            'TABLE_SCHEMA' => array_values($tableSchema)[0],
            'TABLE_NAME'   => $table
        );

        $where = $this->prepareWhere($condition);

        $sql = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE {$where};";

        $query = self::getAssoc($sql);

        return array_values($query);
    }

    public static function getInstance(){
        return new self();
    }
}