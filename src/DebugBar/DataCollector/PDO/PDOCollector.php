<?php

namespace DebugBar\DataCollector\PDO;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use DebugBar\DataCollector\TimeDataCollector;

/**
 * Collects data about SQL statements executed with PDO
 */
class PDOCollector extends DataCollector implements Renderable
{
    protected $connections = array();
    
    protected $timeCollector;

    /**
     * @param TraceablePDO $pdo
     * @param TimeDataCollector $timeCollector
     */
    public function __construct(TraceablePDO $pdo = null, TimeDataCollector $timeCollector = null)
    {
        $this->timeCollector = $timeCollector;
        if ($pdo !== null) {
            $this->addConnection($pdo, 'default');
        }
    }

    /**
     * Adds a new PDO instance to be collector
     * 
     * @param TraceablePDO $pdo
     * @param string $name Optional connection name
     */
    public function addConnection(TraceablePDO $pdo, $name = null)
    {
        if ($name === null) {
            $name = spl_object_hash($pdo);
        }
        $this->connections[$name] = $pdo;
    }

    /**
     * Returns PDO instances to be collected
     * 
     * @return array
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $data = array(
            'nb_statements' => 0,
            'nb_failed_statements' => 0,
            'accumulated_duration' => 0,
            'peak_memory_usage' => 0,
            'statements' => array()
        );

        foreach ($this->connections as $name => $pdo) {
            $pdodata = $this->collectPDO($pdo, $this->timeCollector);
            $data['nb_statements'] += $pdodata['nb_statements'];
            $data['nb_failed_statements'] += $pdodata['nb_failed_statements'];
            $data['accumulated_duration'] += $pdodata['accumulated_duration'];
            $data['peak_memory_usage'] = max($data['peak_memory_usage'], $pdodata['peak_memory_usage']);
            $data['statements'] = array_merge($data['statements'],
                array_map(function($s) use ($name) { $s['connection'] = $name; return $s; }, $pdodata['statements']));
        }

        $data['accumulated_duration_str'] = $this->formatDuration($data['accumulated_duration']);
        $data['peak_memory_usage_str'] = $this->formatBytes($data['peak_memory_usage']);

        return $data;
    }

    /**
     * Collects data from a single TraceablePDO instance
     * 
     * @param TraceablePDO $pdo
     * @param TimeDataCollector $timeCollector
     * @return array
     */
    protected function collectPDO(TraceablePDO $pdo, TimeDataCollector $timeCollector = null)
    {
        $stmts = array();
        foreach ($pdo->getExecutedStatements() as $stmt) {
            $stmts[] = array(
                'sql' => $stmt->getSql(),
                'row_count' => $stmt->getRowCount(),
                'stmt_id' => $stmt->getPreparedId(),
                'prepared_stmt' => $stmt->getSql(),
                'params' => (object) $stmt->getParameters(),
                'duration' => $stmt->getDuration(),
                'duration_str' => $this->formatDuration($stmt->getDuration()),
                'memory' => $stmt->getMemoryUsage(),
                'memory_str' => $this->formatBytes($stmt->getMemoryUsage()),
                'is_success' => $stmt->isSuccess(),
                'error_code' => $stmt->getErrorCode(),
                'error_message' => $stmt->getErrorMessage()
            );
            if ($timeCollector !== null) {
                $timeCollector->addMeasure($stmt->getSql(), $stmt->getStartTime(), $stmt->getEndTime());
            }
        }

        return array(
            'nb_statements' => count($stmts),
            'nb_failed_statements' => count($pdo->getFailedExecutedStatements()),
            'accumulated_duration' => $pdo->getAccumulatedStatementsDuration(),
            'accumulated_duration_str' => $this->formatDuration($pdo->getAccumulatedStatementsDuration()),
            'peak_memory_usage' => $pdo->getPeakMemoryUsage(),
            'peak_memory_usage_str' => $this->formatBytes($pdo->getPeakMemoryUsage()),
            'statements' => $stmts
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'pdo';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return array(
            "database" => array(
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "pdo",
                "default" => "[]"
            ),
            "database:badge" => array(
                "map" => "pdo.nb_statements",
                "default" => 0
            )
        );
    }
}
