<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar;

use ArrayAccess;
use DebugBar\DataCollector\DataCollector;

/**
 * Main DebugBar object
 *
 * Mananges data collectors. DebugBar provides an array-like access
 * to collectors by name.
 *
 * <code>
 *     $debugbar = new DebugBar();
 *     $debugbar->addCollector(new DataCollector\MessagesCollector());
 *     $debugbar['messages']->addMessage("foobar");
 * </code>
 */
class DebugBar implements ArrayAccess
{
    protected $collectors = array();

    protected $data;
    
    protected $jsRenderer;

    /**
     * Adds a data collector
     * 
     * @param DataCollector $collector
     */
    public function addCollector(DataCollector $collector)
    {
        if (isset($this->collectors[$collector->getName()])) {
            throw new DebugBarException("'$name' is already a registered collector");
        }
        $this->collectors[$collector->getName()] = $collector;
        return $this;
    }

    /**
     * Checks if a data collector has been added
     * 
     * @param string $name
     * @return boolean
     */
    public function hasCollector($name)
    {
        return isset($this->collectors[$name]);
    }

    /**
     * Returns a data collector
     * 
     * @param string $name
     * @return DataCollector
     */
    public function getCollector($name)
    {
        if (!isset($this->collectors[$name])) {
            throw new DebugBarException("'$name' is not a registered collector");
        }
        return $this->collectors[$name];
    }

    /**
     * Returns an array of all data collectors
     * 
     * @return array[DataCollector]
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    /**
     * Collects the data from the collectors
     * 
     * @return array
     */
    public function collect()
    {
        $this->data = array();
        foreach ($this->collectors as $name => $collector) {
            $this->data[$name] = $collector->collect();
        }
        return $this->data;
    }

    /**
     * Returns collected data
     *
     * Will collect the data if none have been collected yet
     * 
     * @return array
     */
    public function getData()
    {
        if ($this->data === null) {
            $this->collect();
        }
        return $this->data;
    }

    /**
     * Returns a JavascriptRenderer for this instance
     * 
     * @return JavascriptRenderer
     */
    public function getJavascriptRenderer()
    {
        if ($this->jsRenderer === null) {
            $this->jsRenderer = new JavascriptRenderer($this);
        }
        return $this->jsRenderer;
    }

    // --------------------------------------------
    // ArrayAccess implementation

    public function offsetSet($key, $value)
    {
        throw new DebugBarException("DebugBar[] is read-only");
    }

    public function offsetGet($key)
    {
        return $this->getCollector($key);
    }

    public function offsetExists($key)
    {
        return $this->hasCollector($key);
    }

    public function offsetUnset($key)
    {
        throw new DebugBarException("DebugBar[] is read-only");
    }
}
