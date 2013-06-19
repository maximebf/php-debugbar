# Bridge collectors

DebugBar comes with some "bridge" collectors. This collectors provides a way to integrate
other projets with the DebugBar.

## Monolog

Integrates Monolog messages into the messages view.

    $logger = new Monolog\Logger('mylogger');
    $debugbar->addCollector(new DebugBar\Bridge\MonologCollector($logger));

Note that multiple logger can be collected:

    $debugbar['monolog']->addLogger($logger);

## Propel

Logs propel queries into an SQL queries view. You will need to activate
Propel debug mode.

    // before Propel::init()
    $debugbar->addCollector(new DebugBar\Bridge\PropelCollector());

    Propel::init('/path/to/config');

    // after Propel::init()
    // not mandatory if you set config options by yourself
    DebugBar\Bridge\PropelCollector::enablePropelProfiling();

Queries can be collected on a single connection by providing the `PropelPDO` object
to the `PropelCollector` as first argument.

## Twig

Collects info about rendered templates. You need to wrap your `Twig_Environment` object
into a `DebugBar\Bridge\Twig\TraceableTwigEnvironment` object.

    $loader = new Twig_Loader_Filesystem('.');
    $env = new DebugBar\Bridge\Twig\TraceableTwigEnvironment(new Twig_Environment($loader));
    $debugbar->addCollector(new DebugBar\Bridge\Twig\TwigDataCollector($env));

You can provide a `DebugBar\DataCollector\TimeDataCollector` as the second argument of
`TraceableTwigEnvironment` so render operation can be measured.
