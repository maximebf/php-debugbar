<?php

namespace DebugBar\Tests\ServerHandler;

use DebugBar\ServerHandler\ServerHandlerInterface;

class MockHandler implements ServerHandlerInterface
{
    public $calls = array();

    public function getName()
    {
        return 'mock';
    }

    public function getCommandNames()
    {
        return array('ping');
    }

    public function ping($request, $debugbar)
    {
        $this->calls[] = $request;
        return array('ping' => 'pong');
    }
}