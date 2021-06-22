<?php

namespace DebugBar\Tests\DataCollector;

use DebugBar\Tests\DebugBarTestCase;
use DebugBar\DataCollector\TimeDataCollector;

class TimeDataCollectorTest extends DebugBarTestCase
{
    public function setUp(): void
    {
        $this->s = microtime(true);
        $this->c = new TimeDataCollector($this->s);
    }

    public function testAddMeasure()
    {
        $this->c->addMeasure('foo', $this->s, $this->s + 10, array('a' => 'b'), 'timer');
        $m = $this->c->getMeasures();
        $this->assertCount(1, $m);
        $this->assertEquals('foo', $m[0]['label']);
        $this->assertEquals(10, $m[0]['duration']);
        $this->assertEquals(array('a' => 'b'), $m[0]['params']);
        $this->assertEquals('timer', $m[0]['collector']);
    }

    public function testStartStopMeasure()
    {
        $this->c->startMeasure('foo', 'bar', 'baz');
        usleep(1000);
        $this->c->stopMeasure('foo', array('bar' => 'baz'));
        $m = $this->c->getMeasures();
        $this->assertCount(1, $m);
        $this->assertEquals('bar', $m[0]['label']);
        $this->assertEquals('baz', $m[0]['collector']);
        $this->assertEquals(array('bar' => 'baz'), $m[0]['params']);
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

    public function testMeasure()
    {
        $returned = $this->c->measure('bar', function() {
            return 'returnedValue';
        });
        $m = $this->c->getMeasures();
        $this->assertCount(1, $m);
        $this->assertEquals('bar', $m[0]['label']);
        $this->assertTrue($m[0]['start'] < $m[0]['end']);
        $this->assertSame('returnedValue', $returned);
    }
}
