<?php

include '../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../src/DebugBar/Resources');

$debugbar['messages']->addMessage('I\'m a Deeper Hidden Iframe');

render_demo_page(function() {
?>
<script type="text/javascript">
    $(function() {
        $.get('../ajax.php', function(data) {
            //ajax from IFRAME
        });
    });
</script>
<?php
});
