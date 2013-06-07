<?php

include '../tests/bootstrap.php';

use DebugBar\StandardDebugBar;
use DebugBar\Renderer\JavascriptRenderer;

$debugbar = new StandardDebugBar();
$debugbarRenderer = new JavascriptRenderer($debugbar, '../web/');
