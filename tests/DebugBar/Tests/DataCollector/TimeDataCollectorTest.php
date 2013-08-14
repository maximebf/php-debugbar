<?php

namespace DebugBar\Tests\DataCollector;

use DebugBar\Tests\DebugBarTestCase;
use DebugBar\DataCollector\TimeDataCollector;

class TimeDataCollectorTest extends DebugBarTestCase
{
    public function setUp()
    {
        $this->s = microtime(true);
        $this->c = new TimeDataCollector($this->s);
    }

    public function testAddMeasure()
    {
        $this->c->addMeasure('foo', $this->s, $this->s + 10);
        $m = $this->c->getMeasures();
        $this->assertCount(1, $m);
        $this->assertEquals('foo', $m[0]['label']);
        $this->assertEquals(10, $m[0]['duration']);
    }

    public function testStartStopMeasure()
    {
        $this->c->startMeasure('foo');
        $this->c->stopMeasure('foo');
        $m = $this->c->getMeasures();
        $this->assertCount(1, $m);
        $this->assertEquals('foo', $m[0]['label']);
        $this->assertTrue($m[0]['start'] < $m[0]['end']);
    }

    public function testCollect()
    {
        $this->c->addMeasure('foo', 0, 10);
        $this->c->addMeasure('bar', 10, 20);
        $data = $this->c->collect();
        $this->assertTrue($data['end'] > $this->s);
        $this->assertTrue($data['duration'] > 0);
        $this->assertCount(2, $data['measures']);
    }
}