<?php

include 'bootstrap.php';

$debugbar['messages']->addMessage('hello');

$debugbar['time']->startMeasure('op1', 'sleep 500');
usleep(300);
$debugbar['time']->startMeasure('op2', 'sleep 400');
usleep(200);
$debugbar['time']->stopMeasure('op1');
usleep(200);
$debugbar['time']->stopMeasure('op2');

$debugbar['messages']->addMessage('world', 'warn');
$debugbar['messages']->addMessage(array('toto' => array('titi', 'tata')));
$debugbar['messages']->addMessage('oups', 'error');

$debugbar['time']->startMeasure('render');
?>
<html>
    <head>
        <?php echo $debugbarRenderer->renderHead() ?>
        <script type="text/javascript">
            $(function() {
                $('.ajax').click(function() {
                    var container = $(this).parent().html('...');
                    $.get(this.href, function(data) {
                        container.html(data);
                    });
                    return false;
                });
            });
        </script>
    </head>
    <body>
        <p>PhpDebugBar Demo</p>
        <p><a href="demo_ajax.php" class="ajax">load ajax content</a></p>
        <p><a href="demo_ajax_exception.php" class="ajax">load ajax content with exception</a></p>
        <?php
            usleep(100);
            echo $debugbarRenderer->render();
        ?>
    </body>
</html>
