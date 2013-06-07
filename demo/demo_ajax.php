<?php

include 'bootstrap.php';
$debugbar['messages']->addMessage('hello from ajax');

?>
hello from AJAX
<?php
    $debugbar->collect();
    echo $debugbarRenderer->renderAjaxToolbar();
?>
