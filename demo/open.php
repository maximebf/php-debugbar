<?php

include __DIR__ . '/bootstrap.php';

$openHandler = new DebugBar\OpenHandler($debugbar);
$openHandler->handle();
