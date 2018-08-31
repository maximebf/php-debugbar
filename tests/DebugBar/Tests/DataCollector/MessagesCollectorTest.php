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

    public function testAssets()
    {
        $c = new MessagesCollector();
        $this->assertEmpty($c->getAssets());

        $c->useHtmlVarDumper();
        $this->assertNotEmpty($c->getAssets());
    }

    public function testHtmlMessages()
    {
        $var = array('one', 'two');

        $c = new MessagesCollector();
        $this->assertFalse($c->isHtmlVarDumperUsed());
        $c->addMessage($var);
        $data = $c->collect();
        $message_text = $data['messages'][0]['message'];
        $this->assertContains('array', $message_text);
        $this->assertContains('one', $message_text);
        $this->assertContains('two', $message_text);
        $this->assertNotContains('span', $message_text);
        $this->assertNull($data['messages'][0]['message_html']);

        $c = new MessagesCollector();
        $c->useHtmlVarDumper();
        $this->assertTrue($c->isHtmlVarDumperUsed());
        $c->addMessage($var);
        $data = $c->collect();
        $message_text = $data['messages'][0]['message'];
        $this->assertContains('array', $message_text);
        $this->assertContains('one', $message_text);
        $this->assertContains('two', $message_text);
        $this->assertNotContains('span', $message_text);
        $message_html = $data['messages'][0]['message_html'];
        $this->assertContains('array', $message_html);
        $this->assertContains('one', $message_html);
        $this->assertContains('two', $message_html);
        $this->assertContains('span', $message_html);

    }
}
