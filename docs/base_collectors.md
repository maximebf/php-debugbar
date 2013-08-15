
# Base collectors

Collectors provided in the `DebugBar\DataCollector` namespace.

## Messages

Provides a way to log messages (compatible with [PSR-3 logger](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)).

    $debugbar->addCollector(new DebugBar\DataCollector\MessagesCollector());
    $debugbar['messages']->info('hello world');

You can have multiple messages collector by naming them:

    $debugbar->addCollector(new MessagesCollector('io_ops'));
    $debugbar['io_ops']->info('opening files');

You can aggregate messages collector into other to have a unified view:

    $debugbar['messages']->aggregate($debugbar['io_ops']);

If you don't want to create a standalone tab in the debug bar but still be able
to log messages from a different collector, you don't have to add the collector
to the debug bar:

    $debugbar['messages']->aggregate(new MessagesCollector('io_ops'));

## TimeData

Provides a way to log total execution time as well as taking "measures" (ie. measure the execution time of a particular operation).

    $debugbar->addCollector(new DebugBar\DataCollector\TimeDataCollector());
    
    $debugbar['time']->startMeasure('longop', 'My long operation');
    sleep(2);
    $debugbar['time']->stopMeasure('longop');

    $debugbar['time']->measure('My long operation', function() {
        sleep(2);
    });

Displays the measures on a timeline

## Exceptions

Display exceptions

    $debugbar->addCollector(new DebugBar\DataCollector\ExceptionsCollector());

    try {
        throw new Exception('foobar');
    } catch (Exception $e) {
        $debugbar['exceptions']->addException($e);
    }

## PDO

Logs SQL queries. You need to wrap your `PDO` object into a `DebugBar\DataCollector\PDO\TraceablePDO` object.

    $pdo = new DebugBar\DataCollector\PDO\TraceablePDO(new PDO('sqlite::memory:'));
    $debugbar->addCollector(new DebugBar\DataCollector\PDO\PDOCollector($pdo));

You can even log queries from multiple `PDO` connections:

    $pdoRead  = new DebugBar\DataCollector\PDO\TraceablePDO(new PDO('sqlite::memory:'));
    $pdoWrite = new DebugBar\DataCollector\PDO\TraceablePDO(new PDO('sqlite::memory:'));

    $pdoCollector = new DebugBar\DataCollector\PDO\PDOCollector();
    $pdoCollector->addConnection($pdoRead, 'read-db');
    $pdoCollector->addConnection($pdoWrite, 'write-db');

    $debugbar->addCollector($pdoCollector);

## RequestData

Collects the data of PHP's global variables

    $debugbar->addCollector(new DebugBar\DataCollector\RequestDataCollector());

## Config

Used to display any key/value pairs array

    $data = array('foo' => 'bar');
    $debugbar->addCollector(new DebugBar\DataCollector\ConfigCollector($data));

You can provide a different name for this collector in the second argument of the constructor.

## AggregatedCollector

Aggregates multiple collectors. Do not provide any widgets, you have to add your own controls.

    $debugbar->addCollector(new DebugBar\DataCollector\AggregatedCollector('all_messages', 'messages', 'time'));
    $debugbar['all_messages']->addCollector($debugbar['messages']);
    $debugbar['all_messages']->addCollector(new MessagesCollector('mails'));
    $debugbar['all_messages']['mails']->addMessage('sending mail');

    $renderer = $debugbar->getJavascriptRenderer();
    $renderer->addControl('all_messages', array(
        'widget' => 'PhpDebugBar.Widgets.MessagesWidget',
        'map' => 'all_messages',
        'default' => '[]';
    ));

## Others

Misc collectors which you can just register:

 - `MemoryCollector` (*memory*): Display memory usage
 - `PhpInfoCollector` (*php*): PHP version number
