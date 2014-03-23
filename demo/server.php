<?php

include __DIR__ . '/bootstrap.php';

$server = $debugbar->createServerHandler();
$server->handle();
