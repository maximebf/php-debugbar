<?php

namespace DebugBar\DataCollector\PDO;

/**
 * Holds information about a statement
 */
class TracedStatement
{
    protected $sql;

    protected $rowCount;

    protected $parameters;

    protected $duration;

    protected $memoryUsage;

    protected $exception;

    /**
     * Traces a call and returns a TracedStatement
     * 
     * @param callback $callback
     * @param array $args Callback args
     * @param string $sql The SQL query string
     * @return TracedStatement
     */
    public static function traceCall($callback, array $args, $sql = '')
    {
        $start = microtime(true);
        $result = call_user_func_array($callback, $args);
        $duration = microtime(true) - $start;
        $memoryUsage = memory_get_peak_usage(true);
        $tracedStmt = new TracedStatement($sql, array(), null, 0, $duration, $memoryUsage);
        return array($tracedStmt, $result);
    }

    /**
     * @param string $sql
     * @param array $params
     * @param string $preparedId
     * @param integer $rowCount
     * @param integer $startTime
     * @param integer $endTime
     * @param integer $memoryUsage
     * @param \Exception $e
     */
    public function __construct($sql, array $params = array(), $preparedId = null, $rowCount = 0, $startTime = 0, $endTime = 0, $memoryUsage = 0, \Exception $e = null)
    {
        $this->sql = $sql;
        $this->rowCount = $rowCount;
        $this->parameters = $this->checkParameters($params);
        $this->preparedId = $preparedId;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->duration = $endTime - $startTime;
        $this->memoryUsage = $memoryUsage;
        $this->exception = $e;
    }
	
    /**
     * Check parameters for illegal (non UTF-8) strings, like Binary data.
     *
     * @param $params
     * @return mixed
     */
    public function checkParameters($params)
    {
        foreach ($params as &$param) {
            if(!mb_check_encoding($param, 'UTF-8')) {
                $param = '[BINARY DATA]';
            }
        }
        return $params;
    }

    /**
     * Returns the SQL string used for the query
     * 
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * Returns the SQL string with any parameters used embedded
     *
     * @param string $quotationChar
     * @return string
     */
    public function getSqlWithParams($quotationChar = '<>')
    {
        if (($l = strlen($quotationChar)) > 1) {
            $quoteLeft = substr($quotationChar, 0, $l / 2);
            $quoteRight = substr($quotationChar, $l / 2);
        } else {
            $quoteLeft = $quoteRight = $quotationChar;
        }

        $sql = $this->sql;
        foreach ($this->parameters as $k => $v) {
            $v = "$quoteLeft$v$quoteRight";
            if (!is_numeric($k)) {
                $sql = str_replace($k, $v, $sql);
            } else {
                $p = strpos($sql, '?');
                $sql = substr($sql, 0, $p) . $v. substr($sql, $p + 1);
            }
        }
        return $sql;
    }

    /**
     * Returns the number of rows affected/returned
     * 
     * @return int
     */
    public function getRowCount()
    {
        return $this->rowCount;
    }

    /**
     * Returns an array of parameters used with the query
     * 
     * @return array
     */
    public function getParameters()
    {
        $params = array();
        foreach ($this->parameters as $param) {
            $params[] = htmlentities($param, ENT_QUOTES, 'UTF-8', false);
        }
        return $params;
    }

    /**
     * Returns the prepared statement id
     * 
     * @return string
     */
    public function getPreparedId()
    {
        return $this->preparedId;
    }

    /**
     * Checks if this is a prepared statement
     * 
     * @return boolean
     */
    public function isPrepared()
    {
        return $this->preparedId !== null;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Returns the duration in seconds of the execution
     * 
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Returns the peak memory usage during the execution
     * 
     * @return int
     */
    public function getMemoryUsage()
    {
        return $this->memoryUsage;
    }

    /**
     * Checks if the statement was successful
     * 
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->exception === null;
    }

    /**
     * Returns the exception triggered
     * 
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * Returns the exception's code
     * 
     * @return string
     */
    public function getErrorCode()
    {
        return $this->exception !== null ? $this->exception->getCode() : 0;
    }

    /**
     * Returns the exception's message
     * 
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->exception !== null ? $this->exception->getMessage() : '';
    }
}
