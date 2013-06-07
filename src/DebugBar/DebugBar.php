<?php

namespace DebugBar;

use ArrayAccess;
use DebugBar\DataCollector\DataCollector;
use DebugBar\Renderer\Renderer;
use DebugBar\Renderer\JavascriptRenderer;

class DebugBar implements ArrayAccess
{
    protected $collectors = array();

    protected $data;

    public function addCollector(DataCollector $collector)
    {
        $this->collectors[$collector->getName()] = $collector;
    }

    public function hasCollector($name)
    {
        return isset($this->collectors[$name]);
    }

    public function getCollector($name)
    {
        if (!isset($this->collectors[$name])) {
            throw new DebugBarException("'$name' is not a registered collector");
        }
        return $this->collectors[$name];
    }

    public function getCollectors()
    {
        return $this->collectors;
    }

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

    public function collect()
    {
        $this->data = array();
        foreach ($this->collectors as $name => $collector) {
            $this->data[$name] = $collector->collect();
        }
        return $this->data;
    }

    public function getData()
    {
        return $this->data;
    }
}
