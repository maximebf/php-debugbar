<?php

error_reporting(E_ALL | E_STRICT);

$loader = require(dirname(__DIR__) . '/vendor/autoload.php');
$loader->add('DebugBar\Tests', __DIR__);
