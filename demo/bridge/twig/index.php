<?php

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/../../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../../src/DebugBar/Resources');

$loader = new Twig_Loader_Filesystem('.');
$twig = new DebugBar\Bridge\Twig\TraceableTwigEnvironment(new Twig_Environment($loader), $debugbar['time']);

$debugbar->addCollector(new DebugBar\Bridge\Twig\TwigCollector($twig));

render_demo_page(function() use ($twig) {
    echo $twig->render('hello.html', array('name' => 'peter pan'));
});
