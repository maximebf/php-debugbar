<?php

namespace DebugBar\Tests;

use DebugBar\HttpDriverInterface;

class MockHttpDriver implements HttpDriverInterface
{
    public $headers = array();

    public $sessionStarted = true;

    public $session = array();

    function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    function isSessionStarted()
    {
        return $this->sessionStarted;
    }

    function setSessionValue($name, $value)
    {
        $this->session[$name] = $value;
    }

    function hasSessionValue($name)
    {
        return array_key_exists($name, $this->session);
    }

    function getSessionValue($name)
    {
        return $this->session[$name];
    }

    function deleteSessionValue($name)
    {
        unset($this->session[$name]);
    }
}
