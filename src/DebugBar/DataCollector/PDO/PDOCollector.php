<?php

namespace DebugBar\DataCollector\PDO;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Collects data about SQL statements executed with PDO
 */
class PDOCollector extends DataCollector implements Renderable
{
    protected $pdo;
    
    /**
     * @param TraceablePDO $pdo
     */
    public function __construct(TraceablePDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $stmts = array();
        foreach ($this->pdo->getExecutedStatements() as $stmt) {
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
        }

        return array(
            'nb_statements' => count($stmts),
            'nb_failed_statements' => count($this->pdo->getFailedExecutedStatements()),
            'accumulated_duration' => $this->pdo->getAccumulatedStatementsDuration(),
            'accumulated_duration_str' => $this->formatDuration($this->pdo->getAccumulatedStatementsDuration()),
            'peak_memory_usage' => $this->pdo->getPeakMemoryUsage(),
            'peak_memory_usage_str' => $this->formatBytes($this->pdo->getPeakMemoryUsage()),
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
