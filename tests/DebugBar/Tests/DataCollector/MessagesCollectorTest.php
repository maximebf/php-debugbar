<?php

namespace DebugBar\Tests\DataCollector;

use DebugBar\Tests\DebugBarTestCase;
use DebugBar\DataCollector\MessagesCollector;

class MessagesCollectorTest extends DebugBarTestCase
{
    public function testAddMessageAndLog()
    {
        $c = new MessagesCollector();
        $c->addMessage('foobar');
        $msgs = $c->getMessages();
        $this->assertCount(1, $msgs);
        $c->log('notice', 'hello');
        $this->assertCount(2, $c->getMessages());
    }

    public function testAggregate()
    {
        $a = new MessagesCollector('a');
        $c = new MessagesCollector('c');
        $c->aggregate($a);
        $c->addMessage('message from c');
        $a->addMessage('message from a');
        $msgs = $c->getMessages();
        $this->assertCount(2, $msgs);
        $this->assertArrayHasKey('collector', $msgs[1]);
        $this->assertEquals('a', $msgs[1]['collector']);
    }

    public function testCollect()
    {
        $c = new MessagesCollector();
        $c->addMessage('foo');
        $data = $c->collect();
        $this->assertEquals(1, $data['count']);
        $this->assertEquals($c->getMessages(), $data['messages']);
    }
}