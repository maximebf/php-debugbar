<?php

namespace DebugBar\Tests\DataCollector;

use DebugBar\Tests\DebugBarTestCase;
use DebugBar\DebugBar;
use DebugBar\DataCollector\ConfigCollector;

class ConfigCollectorTest extends DebugBarTestCase
{
    public function testCollect()
    {
        $c = new ConfigCollector(array('s' => 'bar', 'a' => array(), 'o' => new \stdClass()));
        $data = $c->collect();
        $this->assertArrayHasKey('s', $data);
        $this->assertEquals('bar', $data['s']);
        $this->assertArrayHasKey('a', $data);
        $this->assertEquals("[]", $data['a']);
        $this->assertArrayHasKey('o', $data);
    }

    public function testName()
    {
        $c = new ConfigCollector(array(), 'foo');
        $this->assertEquals('foo', $c->getName());
        $this->assertArrayHasKey('foo', $c->getWidgets());
    }
}
