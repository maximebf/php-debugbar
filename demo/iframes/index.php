<?php

include '../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../src/DebugBar/Resources');

$debugbar['messages']->addMessage('Top Page(Main debugbar)');

render_demo_page(function() {
?>
<iframe src="iframe1.php" height="350" style="width:100%;"></iframe>
<?php
});
