<?php

include 'bootstrap.php';
$debugbar['messages']->addMessage('hello from ajax');
$debugbar->sendDataInHeaders();
?>
hello from AJAX