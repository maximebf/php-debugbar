<?php

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/../../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../../src/DebugBar/Resources');

$cache = new CacheCache\Cache(new CacheCache\Backends\Memory());

$debugbar->addCollector(new DebugBar\Bridge\CacheCache\CacheCacheCollector($cache));