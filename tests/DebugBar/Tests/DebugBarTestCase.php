<?php

namespace DebugBar\Tests;

use DebugBar\DebugBar;
use DebugBar\RandomRequestIdGenerator;

abstract class DebugBarTestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->debugbar = new DebugBar();
    }
}
