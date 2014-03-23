<?php

include __DIR__ . '/bootstrap.php';

$fc = $debugbar->createServerHandlerFrontController();
$fc->handle();
