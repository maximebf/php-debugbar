<?php

namespace DebugBar\Tests;

use DebugBar\DebugBar;
use DebugBar\RandomRequestIdGenerator;

abstract class DebugBarTestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->debugbar = new DebugBar();
        $this->debugbar->setHttpDriver($http = new MockHttpDriver());
    }

    public function assertJsonIsArray($json)
    {
        $data = json_decode($json);
        $this->assertTrue(is_array($data));
    }

    public function assertJsonIsObject($json)
    {
        $data = json_decode($json);
        $this->assertTrue(is_object($data));
    }

    public function assertJsonArrayNotEmpty($json)
    {
        $data = json_decode($json, true);
        $this->assertTrue(is_array($data) && !empty($data));
    }

    public function assertJsonHasProperty($json, $property)
    {
        $data = json_decode($json, true);
        $this->assertTrue(array_key_exists($property, $data));
    }

    public function assertJsonPropertyEquals($json, $property, $expected)
    {
        $data = json_decode($json, true);
        $this->assertTrue(array_key_exists($property, $data));
        $this->assertEquals($expected, $data[$property]);
    }
}
