# Bridge collectors

DebugBar comes with some "bridge" collectors. This collectors provides a way to integrate
other projects with the DebugBar.

## CacheCache

http://maximebf.github.io/CacheCache/

Displays cache operations using `DebugBar\Bridge\CacheCacheCollector`

    $cache = new CacheCache\Cache(new CacheCache\Backends\Memory());
    $debugbar->addCollector(new DebugBar\Bridge\CacheCacheCollector($cache));

CacheCache uses [Monolog](https://github.com/Seldaek/monolog) for logging,
thus it is required to collect data.

`CacheCacheCollector` subclasses `MonologCollector`, thus it can be
[aggregated in the messages view](base-collectors.html#messages).

## Doctrine

http://doctrine-project.org

Displays sql queries into an SQL queries view using `DebugBar\Bridge\DoctrineCollector`.
You will need to set a `Doctrine\DBAL\Logging\DebugStack` logger on your connection.

    $debugStack = new Doctrine\DBAL\Logging\DebugStack();
    $entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);
    $debugbar->addCollector(new DebugBar\Bridge\DoctrineCollector($debugStack));

`DoctrineCollector` also accepts an `Doctrine\ORM\EntityManager` as argument
provided the `SQLLogger` is a ̀DebugStack`.

## Monolog

https://github.com/Seldaek/monolog

Integrates Monolog messages into a message view using `DebugBar\Bridge\MonologCollector`.

    $logger = new Monolog\Logger('mylogger');
    $debugbar->addCollector(new DebugBar\Bridge\MonologCollector($logger));

Note that multiple logger can be collected:

    $debugbar['monolog']->addLogger($logger);

`MonologCollector` can be [aggregated](base-collectors.html#messages) into the `MessagesCollector`.

## Propel

http://propelorm.org/

Displays propel queries into an SQL queries view using `DebugBar\Bridge\PropelCollector`.
You will need to activate Propel debug mode.

    // before Propel::init()
    $debugbar->addCollector(new DebugBar\Bridge\PropelCollector());

    Propel::init('/path/to/config');

    // after Propel::init()
    // not mandatory if you set config options by yourself
    DebugBar\Bridge\PropelCollector::enablePropelProfiling();

Queries can be collected on a single connection by providing the `PropelPDO` object
to the `PropelCollector` as first argument.

## Slim

http://slimframework.com

Displays message from the Slim logger into a message view using `DebugBar\Bridge\SlimCollector`.

    $app = new Slim\Slim();
    $debugbar->addCollector(new DebugBar\Bridge\SlimCollector($app));

## Swift Mailer

http://swiftmailer.org/

Display log messages and sent mail using `DebugBar\Bridge\SwiftMailer\SwiftLogCollector` and
`DebugBar\Bridge\SwiftMailer\SwiftMailCollector`.

    $mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());
    $debugbar['messages']->aggregate(new DebugBar\Bridge\SwiftMailer\SwiftLogCollector($mailer));
    $debugbar->addCollector(new DebugBar\Bridge\SwiftMailer\SwiftMailCollector($mailer));

## Twig

http://twig.sensiolabs.org/

This collector uses the class `Twig_Extension_Profiler` to collect info about rendered
templates, blocks and macros.
You need to inject the root `Twig_Profiler_Profile` into the collector:

    $loader = new Twig_Loader_Filesystem('.');
    $env = new Twig_Environment($loader);
    $profile = new Twig_Profiler_Profile();
    $env->addExtension(new Twig_Extension_Profiler($profile));
    $debugbar->addCollector(new DebugBar\Bridge\TwigProfileCollector($profile));

You can optionally use `DebugBar\Bridge\Twig\TimeableTwigExtensionProfiler` in place of
`Twig_Extension_Profiler` so render operation can be measured.

    $loader = new Twig_Loader_Filesystem('.');
    $env = new Twig_Environment($loader);
    $profile = new Twig_Profiler_Profile();
    $env->addExtension(new DebugBar\Bridge\Twig\TimeableTwigExtensionProfiler($profile, $debugbar['time']));
    $debugbar->addCollector(new DebugBar\Bridge\TwigProfileCollector($profile));

