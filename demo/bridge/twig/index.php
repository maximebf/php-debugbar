<?php

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/../../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../../src/DebugBar/Resources');

$loader = new Twig\Loader\FilesystemLoader('.');
$twig = new Twig\Environment($loader);
$profile = new Twig\Profiler\Profile();
$twig->addExtension(new DebugBar\Bridge\Twig\TimeableTwigExtensionProfiler($profile, $debugbar['time']));

$debugbar->addCollector(new DebugBar\Bridge\NamespacedTwigProfileCollector($profile, $twig));

render_demo_page(function() use ($twig) {
    echo $twig->render('hello.html', array('name' => 'peter pan'));
});
