<?php

include '../tests/bootstrap.php';

use DebugBar\StandardDebugBar;

$debugbar = new StandardDebugBar();
$debugbarRenderer = $debugbar->getJavascriptRenderer()->setBaseUrl('../src/DebugBar/Resources');
