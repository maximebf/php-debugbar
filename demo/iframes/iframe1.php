<?php

include '../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../src/DebugBar/Resources');

$debugbar['messages']->addMessage('I\'m a IFRAME');

render_demo_page(function() {
?>
<iframe src="iframe2.php" style="display:none;"></iframe>
<?php
});
