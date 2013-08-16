<?php

namespace DebugBar\Tests;

use DebugBar\DebugBar;
use DebugBar\OpenHandler;
use DebugBar\Tests\Storage\MockStorage;

class OpenHandlerTest extends DebugBarTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->debugbar->setStorage(new MockStorage(array('foo' => array('__meta' => array('id' => 'foo')))));
        $this->openHandler = new OpenHandler($this->debugbar);
    }

    public function testFind()
    {
        $request = array();
        $result = $this->openHandler->handle($request, false, false);
        $this->assertJsonArrayNotEmpty($result);
    }

    public function testGet()
    {
        $request = array('op' => 'get', 'id' => 'foo');
        $result = $this->openHandler->handle($request, false, false);
        $this->assertJsonIsObject($result);
        $this->assertJsonHasProperty($result, '__meta');
        $data = json_decode($result, true);
        $this->assertEquals('foo', $data['__meta']['id']);
    }

    /**
     * @expectedException \DebugBar\DebugBarException
     */
    public function testGetMissingId()
    {
        $this->openHandler->handle(array('op' => 'get'), false, false);
    }

    public function testClear()
    {
        $result = $this->openHandler->handle(array('op' => 'clear'), false, false);
        $this->assertJsonPropertyEquals($result, 'success', true);
    }
}