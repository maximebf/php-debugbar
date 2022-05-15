<?php

namespace DebugBar\Tests;

class TestMessage
{
    /**
     * @var string
     */
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function __toString()
    {
        return $this->message;
    }
}
