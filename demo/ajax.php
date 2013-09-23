<?php

include 'bootstrap.php';
$debugbar['messages']->addMessage('hello from ajax');
$debugbar->sendDataInHeaders(true);
?>
hello from AJAX