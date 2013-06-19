<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\Bridge;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;

/**
 * A monolog handler as well as a data collector
 *
 * <code>
 * $debugbar->addCollector(new MonologCollector($logger));
 * </code>
 */
class MonologCollector extends AbstractProcessingHandler implements DataCollectorInterface, Renderable
{
    protected $records = array();

    public function __construct(Logger $logger, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $logger->pushHandler($this);
    }

    public function addLogger(Logger $logger)
    {
        $logger->pushHandler($this);
    }

    protected function write(array $record)
    {
        $this->records[] = array(
            'message' => $record['formatted'],
            'is_string' => true,
            'label' => strtolower($record['level_name']),
            'time' => $record['datetime']->format('U')
        );
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        return array(
            'count' => count($this->records),
            'records' => $this->records
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'monolog';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return array(
            "logs" => array(
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "monolog.records",
                "default" => "[]"
            ),
            "logs:badge" => array(
                "map" => "monolog.count",
                "default" => "null"
            )
        );
    }
}
