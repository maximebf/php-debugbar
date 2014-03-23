<?php

namespace DebugBar\Tests\ServerHandler;

use DebugBar\Tests\DebugBarTestCase;
use DebugBar\ServerHandler\FrontController;

class FrontControllerTest extends DebugBarTestCase
{
    public function testRegisterHandler()
    {
        $fc = new FrontController($this->debugbar);
        $fc->registerHandler(new MockHandler());
        $this->assertTrue($fc->isHandlerRegistered('mock'));
    }

    public function testHandle()
    {
        $fc = new FrontController($this->debugbar);
        $fc->registerHandler($m = new MockHandler());

        $this->assertEquals('"pong"', $fc->handle(array('hdl' => 'debugbar', 'op' => 'ping'), false, false));
        $this->assertEquals('{"ping":"pong"}', $fc->handle(array('hdl' => 'mock', 'op' => 'ping', 'param' => 'val'), false, false));
        $this->assertArrayHasKey('param', $m->calls[0]);
    }
}
