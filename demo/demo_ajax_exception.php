<?php

include 'bootstrap.php';

try {
    throw new Exception('Something failed!');
} catch (Exception $e) {
    $debugbar['messages']->addMessage($e, 'error');
}

?>
error from AJAX
<?php
    $debugbar->collect();
    echo $debugbarRenderer->renderAjaxToolbar();
?>
