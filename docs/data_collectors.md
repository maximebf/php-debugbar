# Collecting Data

## Using collectors

Collectors can be added to your debug bar using `addCollector()`. 

    $debugbar = new DebugBar();
    $debugbar->addCollector(new DataCollector\RequestDataCollector());

Each collector as a unique name as defined by its `getName()` method. You can 
access collectors using `getCollector($name)`.

    $debugbar->addCollector(new DataCollector\MessagesCollector());
    $debugbar->getCollector('messages')->addMessage("foobar");
    // or:
    $debugbar['messages']->addMessage("foobar");

Data will be collected from them when the debug bar is rendered. You can however
collect the data earlier using `collect()`.

    $debugbar->collect();

## Creating collectors

Collectors must implement the `DebugBar\DataCollector\DataCollectorInterface`. They
may subclass `DebugBar\DataCollector\DataCollector` which provides utility methods.

Collectors must provide a `getName()` function returning their unique name and a
`collect()` function returning some json-encodable data. The latter will be called at the
same time the `DebugBar::collect()` method is called.

    class MyDataCollector extends DebugBar\DataCollector\DataCollector
    {
        public function collect()
        {
            return array("uniqid" => uniqid());
        }

        public function getName()
        {
            return 'mycollector';
        }
    }

    $debugbar->addCollector(new MyDataCollector());

This however won't show anything in the debug bar as no information are provided
on how to display these data. You could do that manually as you'll see in later chapter
or implement the `DebugBar\DataSource\Renderable` interface. 

To implement it, you must define a `getWidgets()` function which returns an array
of key/value pairs where key are control names and values control options as defined
in `JavascriptRenderer::addControl($name, $options)` (see Rendering chapter).

    class MyDataCollector extends DebugBar\DataCollector\DataCollector implements DebugBar\DataCollector\Renderable
    {
        // ...

        public function getWidgets()
        {
            return array(
                "mycollector" => array(
                    "icon" => "cogs",
                    "tooltip" => "uniqid()",
                    "map" => "uniqid",
                    "default" => "''"
                )
            );
        }
    }

This will have the result of adding a new indicator to the debug bar.

## Base collectors

Cpllectors provided in the `DebugBar\DataCollector` namespace.

### Messages

Provides a way to log messages (compotible with [PSR-3 logger](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)).

    $debugbar->addCollector(new MessagesCollector());
    $debugbar['messages']->info('hello world');

You can have multiple messages collector by naming them:

    $debugbar->addCollector(new MessagesCollector('io_ops'));
    $debugbar['io_ops']->info('opening files');

You can aggregate messages collector into other to have a unified view:

    $debugbar['messages']->aggregate($debugbar['io_ops']);

### TimeData

Provides a way to log total execution time as well as taking "measures" (ie. measure the execution time of a particular operation).

    $debugbar->addCollector(new TimeDataCollector());
    
    $debugbar['time']->startMeasure('longop', 'My long operation');
    sleep(2);
    $debugbar['time']->stopMeasure('longop');

    $debugbar['time']->measure('My long operation', function() {
        sleep(2);
    });

Displays the measures on a timeline

### Exceptions

Display exceptions

    $debugbar->addCollector(new ExceptionsCollector());

    try {
        throw new Exception('foobar');
    } catch (Exception $e) {
        $debugbar['exceptions']->addException($e);
    }

### PDO

Logs SQL queries. You need to wrap your `PDO` object into a `DebugBar\DataCollector\PDO\TraceablePDO` object.

    $pdo = new PDO\TraceablePDO(new PDO('sqlite::memory:'));
    $debugbar->addCollector(new PDO\PDOCollector($pdo));

### RequestDataCollector

Collects the data of PHP's global variables

    $debugbar->addCollector(new RequestDataCollector());

### AggregatedCollector

Aggregates multiple collectors. Do not provide any widgets, you have to add your own controls.

    $debugbar->addCollector(new AggregatedCollector('all_messages', 'messages', 'time'));
    $debugbar['all_messages']->addCollector($debugbar['messages']);
    $debugbar['all_messages']->addCollector(new MessagesCollector('mails'));
    $debugbar['all_messages']['mails']->addMessage('sending mail');

    $renderer = $debugbar->getJavascriptRenderer();
    $renderer->addControl('all_messages', array(
        'widget' => 'PhpDebugBar.Widgets.MessagesWidget',
        'map' => 'all_messages',
        'default' => '[]';
    ));

### Others

Misc collectors which you can just register:

 - `MemoryCollector` (*memory*): Display memory usage
 - `PhpInfoCollector` (*php*): PHP version number
