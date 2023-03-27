<?php


namespace GKTOMK\Models\Systematika;


class Expr
{
    /**
     * @var string
     */
    protected $sql;

    /**
     * @var array
     */
    protected $params = array();

    public function __construct($sql) {
        $this->sql = $sql;
    }

    public function __toString() {
        return $this->composeSql();
    }

    protected function composeSql() {
        return Util::replaceTokens($this->getPreparedSql(), $this->getPreparedParams());
    }

    protected function getPreparedParams() {
        return $this->params;
    }

    protected function getPreparedSql() {
        return $this->sql;
    }

    public function setParams(array $params) {
        $this->params = $params;
        return $this;
    }
}