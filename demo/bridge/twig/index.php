<?php

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/../../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../../src/DebugBar/Resources');

$loader = new Twig_Loader_Filesystem('.');
$twig = new Twig_Environment($loader);
$profile = new Twig_Profiler_Profile();
$twig->addExtension(new DebugBar\Bridge\Twig\TimeableTwigExtensionProfiler($profile, $debugbar['time']));

$debugbar->addCollector(new DebugBar\Bridge\TwigProfileCollector($profile));

render_demo_page(function() use ($twig) {
    echo $twig->render('hello.html', array('name' => 'peter pan'));
});
