<?php

namespace DebugBar\Tests\ServerHandler;

use DebugBar\Tests\DebugBarTestCase;
use DebugBar\DebugBar;
use DebugBar\ServerHandler\OpenHandler;
use DebugBar\Tests\Storage\MockStorage;

class OpenHandlerTest extends DebugBarTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->debugbar->setStorage(new MockStorage(array('foo' => array('__meta' => array('id' => 'foo')))));
        $this->openHandler = new OpenHandler();
    }

    public function testFind()
    {
        $data = $this->openHandler->find(array(), $this->debugbar);
        $this->assertNotEmpty($data);
    }

    public function testGet()
    {
        $data = $this->openHandler->get(array('id' => 'foo'), $this->debugbar);
        $this->assertArrayHasKey('__meta', $data);
        $this->assertEquals('foo', $data['__meta']['id']);
    }

    /**
     * @expectedException \DebugBar\DebugBarException
     */
    public function testGetMissingId()
    {
        $this->openHandler->get(array(), $this->debugbar);
    }

    public function testClear()
    {
        $data = $this->openHandler->clear(array(), $this->debugbar);
        $this->assertArrayHasKey('success', $data);
    }
}
