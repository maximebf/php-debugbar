<?php

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/../../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../../src/DebugBar/Resources');

$loader = new Twig\Loader\FilesystemLoader('.');
$twig = new Twig\Environment($loader);
$profile = new Twig\Profiler\Profile();

// enable template measure on timeline
$twig->addExtension(new DebugBar\Bridge\Twig\TimeableTwigExtensionProfiler($profile, $debugbar['time']));

// enable {% measure 'foo' %} {% endmeasure %} tags for time measure on templates
$twig->addExtension(new DebugBar\Bridge\Twig\MeasureTwigExtension($debugbar));

// enable {{ debugbar_dump('foo') }} function on templates
$twig->addExtension(new DebugBar\Bridge\Twig\DumpTwigExtension());

// enable {{ debugbar_debug('foo') }} function on templates
$twig->enableDebug();
$twig->addExtension(new DebugBar\Bridge\Twig\DebugTwigExtension($debugbar));

$debugbar->addCollector(new DebugBar\Bridge\NamespacedTwigProfileCollector($profile, $twig));

render_demo_page(function() use ($twig) {
    echo $twig->render('hello.html', array('name' => 'peter pan'));
});
