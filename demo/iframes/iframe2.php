<?php

include '../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../src/DebugBar/Resources');

$debugbar['messages']->addMessage('I\'m a Deeper Hidden Iframe');

render_demo_page(function() {});
