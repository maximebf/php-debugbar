<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\DataCollector;

use Exception;

/**
 * Collects info about exceptions
 */
class ExceptionsCollector extends DataCollector implements Renderable
{
    protected $exceptions = array();

    /**
     * Adds an exception to be profiled in the debug bar
     * 
     * @param Exception $e
     */
    public function addException(Exception $e)
    {
        $this->exceptions[] = $e;
    }

    /**
     * Returns the list of exceptions being profiled
     * 
     * @return array[Exception]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        return array(
            'count' => count($this->exceptions),
            'exceptions' => array_map(array($this, 'formatExceptionData'), $this->exceptions)
        );
    }

    /**
     * Returns exception data as an array
     * 
     * @param Exception $e
     * @return array
     */
    public function formatExceptionData(Exception $e)
    {
        $lines = file($e->getFile());
        $start = $e->getLine() - 4;
        $lines = array_slice($lines, $start < 0 ? 0 : $start, 7);

        return array(
            'type' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'surrounding_lines' => $lines
        );
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'exceptions';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return array(
            'exceptions' => array(
                'widget' => 'PhpDebugBar.Widgets.ExceptionsWidget',
                'map' => 'exceptions.exceptions',
                'default' => '[]'
            ),
            'exceptions:badge' => array(
                'map' => 'exceptions.count',
                'default' => 'null'
            )
        );
    }
}
