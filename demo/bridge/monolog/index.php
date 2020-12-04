<?php

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/../../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../../src/DebugBar/Resources');

$logger = new Monolog\Logger('demo');

$debugbar->addCollector(new DebugBar\Bridge\MonologCollector($logger));

$logger->info('hello world');

render_demo_page();
