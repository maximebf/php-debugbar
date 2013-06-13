<?php

include 'bootstrap.php';
$debugbar['messages']->addMessage('hello from ajax');

?>
hello from AJAX
<?php
    echo $debugbarRenderer->render(false);
?>
