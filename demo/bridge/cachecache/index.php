<?php

include __DIR__ . '/bootstrap.php';

$cache->set('foo', 'bar');
$cache->get('foo');
$cache->get('bar');

render_demo_page();
