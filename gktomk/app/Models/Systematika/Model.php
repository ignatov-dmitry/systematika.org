<?php


namespace GKTOMK\Models\Systematika;


use ArrayAccess;
use GKTOMK\Models\DB;
use InvalidArgumentException;
use LogicException;
use RedBeanPHP\R;

class Model
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

    protected string $tableName = '';

    public function getItems($criteria = array(), $selectedFields = ['*'], $orderBy = null, $limit = null): ?array
    {
        $sql = '
            SELECT {select}
            FROM {table}
            WHERE {where}
        ';

        if ($orderBy) $sql .= ' ORDER BY {order}';
        if ($limit) $sql .= ' LIMIT {limit}';

        $whereCondition = '';

        if ($criteria){
            $whereCondition = $this->prepareWhere($criteria);
        }

        $sql = Util::replaceTokens($sql, [
            'select' => implode(',', $selectedFields),
            'table'  => $this->getTableName(),
            'where'  => $whereCondition ? : 1,
            'order'  => $orderBy,
            'limit'  => $limit
        ]);

        return DB::getAll($sql);
    }

    public function getItem($criteria = array(), $selectedFields = ['*'], $orderBy = null)
    {
        $item = $this->getItems($criteria, $selectedFields, $orderBy, 1);

        if ($item)
            $item = $item[0];

        return $item;
    }

    public function getTableName(): string
    {
        return $this->tableName;
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
        $tableSchema = R::getAll('SELECT DATABASE()');

        $condition = array(
            'TABLE_SCHEMA' => array_values($tableSchema)[0],
            'TABLE_NAME'   => $table
        );

        $where = $this->prepareWhere($condition);

        $sql = "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE {$where};";

        $query = R::getAssoc($sql);

        return array_values($query);
    }

    public static function getColumnValues($array = [], $columns = []): array
    {
        return array_map(function ($item) use ($columns){
            $cleanItem = [];
            foreach ($columns as $column)
                $cleanItem[$column] = $item[$column];

            return $cleanItem;

        }, $array);
    }

    /**
     * Builds a criterion for criteria.
     *
     * @param $key
     * @param $val
     * @param string $op
     * @return array
     */
    public static final function cond($key, $val, $op = self::OP_EQUAL) {
        return array(
            'key' => $key,
            'val' => $val,
            'op'  => $op
        );
    }

    public final function prepareSelect(array $fields) {
        $tpl      = '%s AS %s';
        $prepared = array();

        foreach ($fields as $alias => $field) {
            if ($field instanceof Expr) $field = $this->prepareVal($field);
            $field = (string) $field;
            $prepared[] = is_string($alias) ? sprintf($tpl, $field, $alias) : $field;
        }

        return implode(', ', $prepared);
    }

    public function prepareJoin(array $criteria) {
        $format = "%s JOIN %s %s ON %s";
        $joinType = $criteria['type'];
        $table = $criteria['table'];
        $alias = $this->ifx($criteria, 'alias');
        $conditions = $this->ifx($criteria, 'conditions');
        $sql = sprintf($format, $joinType, $table, $alias, $conditions);
        return $sql;
    }

    public static function getInstance()
    {
        return new static();
    }

}
