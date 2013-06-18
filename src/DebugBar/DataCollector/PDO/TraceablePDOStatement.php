<?php

namespace DebugBar\DataCollector\PDO;

use PDO;
use PDOStatement;
use PDOException;

/**
 * A traceable PDO statement to use with Traceablepdo
 */
class TraceablePDOStatement extends PDOStatement
{
    protected $pdo;

    protected $boundParameters = array();

    protected function __construct(TraceablePDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritDoc}
     */
    public function bindColumn($column, &$param) {
        $this->boundParameters[$column] = $param;
        $args = array_merge(array($column, &$param), array_slice(func_get_args(), 2));
        return call_user_func_array(array("parent", 'bindColumn'), $args);
    }

    /**
     * {@inheritDoc}
     */
    public function bindParam($param, &$var) {
        $this->boundParameters[$param] = $var;
        $args = array_merge(array($param, &$var), array_slice(func_get_args(), 2));
        return call_user_func_array(array("parent", 'bindParam'), $args);
    }

    /**
     * {@inheritDoc}
     */
    public function bindValue($param, $value) {
        $this->boundParameters[$param] = $value;
        return call_user_func_array(array("parent", 'bindValue'), func_get_args());
    }

    /**
     * {@inheritDoc}
     */
    public function execute($params = array())
    {
        $start = microtime(true);
        $ex = null;

        try {
            $result = parent::execute($params);
        } catch (PDOException $e) {
            $ex = $e;
        }

        $preparedId = spl_object_hash($this);
        $boundParameters = array_merge($this->boundParameters, $params);
        $duration = microtime(true) - $start;
        $memoryUsage = memory_get_usage(true);
        if ($this->pdo->getAttribute(PDO::ATTR_ERRMODE) !== PDO::ERRMODE_EXCEPTION && $result === false) {
            $error = $this->errorInfo();
            $ex = new PDOException($error[2], $error[0]);
        }

        $tracedStmt = new TracedStatement($this->queryString, $boundParameters, 
            $preparedId, $this->rowCount(), $duration, $memoryUsage, $ex);
        $this->pdo->addExecutedStatement($tracedStmt);

        if ($this->pdo->getAttribute(PDO::ATTR_ERRMODE) === PDO::ERRMODE_EXCEPTION && $ex !== null) {
            throw $ex;
        }
        return $result;
    }
}
