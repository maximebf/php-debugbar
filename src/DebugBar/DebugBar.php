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
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\Storage\StorageInterface;

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

    protected $requestIdGenerator;

    protected $requestId;

    protected $storage;

    /**
     * Adds a data collector
     *
     * @param DataCollectorInterface $collector
     *
     * @throws DebugBarException
     * @return $this
     */
    public function addCollector(DataCollectorInterface $collector)
    {
        if ($collector->getName() === '__meta') {
            throw new DebugBarException("'__meta' is a reserved name and cannot be used as a collector name");
        }
        if (isset($this->collectors[$collector->getName()])) {
            throw new DebugBarException("'{$collector->getName()}' is already a registered collector");
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
     * @return DataCollectorInterface
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
     * @return array[DataCollectorInterface]
     */
    public function getCollectors()
    {
        return $this->collectors;
    }

    /**
     * Sets the request id generator
     * 
     * @param RequestIdGeneratorInterface $generator
     */
    public function setRequestIdGenerator(RequestIdGeneratorInterface $generator)
    {
        $this->requestIdGenerator = $generator;
        return $this;
    }

    /**
     * @return RequestIdGeneratorInterface
     */
    public function getRequestIdGenerator()
    {
        if ($this->requestIdGenerator === null) {
            $this->requestIdGenerator = new RequestIdGenerator();
        }
        return $this->requestIdGenerator;
    }

    /**
     * Returns the id of the current request
     * 
     * @return string
     */
    public function getCurrentRequestId()
    {
        if ($this->requestId === null) {
            $this->requestId = $this->getRequestIdGenerator()->generate();
        }
        return $this->requestId;
    }

    /**
     * Sets the storage backend to use to store the collected data
     * 
     * @param StorageInterface $storage
     */
    public function setStorage(StorageInterface $storage = null)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @return StorageInterface
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Checks if the data will be persisted
     * 
     * @return boolean
     */
    public function isDataPersisted()
    {
        return $this->storage !== null;
    }

    /**
     * Collects the data from the collectors
     * 
     * @return array
     */
    public function collect()
    {
        $this->data = array(
            '__meta' => array(
                'id' => $this->getCurrentRequestId(),
                'datetime' => date('Y-m-d H:i:s'),
                'utime' => microtime(true),
                'uri' => isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null,
                'ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null
            )
        );

        foreach ($this->collectors as $name => $collector) {
            $this->data[$name] = $collector->collect();
        }

        if ($this->storage !== null) {
            $this->storage->save($this->getCurrentRequestId(), $this->data);
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
