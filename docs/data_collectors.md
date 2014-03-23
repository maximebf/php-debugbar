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
or implement the `DebugBar\DataSource\WidgetProvider` interface.

To implement it, you must define a `getWidgets()` function which returns an array
of key/value pairs where key are widget names and values widget objects (of type
`DebugBar\Widget\Tab`, `DebugBar\Widget\Indicator` or `DebugBar\Widget\DataMap`).

    class MyDataCollector extends DebugBar\DataCollector\DataCollector implements DebugBar\DataCollector\WidgetProvider
    {
        // ...

        public function getWidgets()
        {
            return array(
                "mycollector" => new DebugBar\Widget\Indicator("cogs", "uniqid", "''", "uniqid()")
            );
        }
    }

This will have the result of adding a new indicator to the debug bar.

When implementing the WidgetProvider interface, you may use widgets which are not provided
with the default install. You can add new assets by implementing the `DebugBar\DataCollector\AssetProvider` interface.

To implement it, you must define the `getAssets()` method. It must return an array with the
following keys:

 - base\_path: base path of assets (optional, if omitted or null, will use the base path of the JavascriptRenderer)
 - base\_url: base url of assets (optional, same as base\_path)
 - css: an array of css filenames
 - js: an array of javascript filenames

Example:

    class MyDbCollector extends DebugBar\DataCollector\DataCollector implements DebugBar\DataCollector\WidgetProvider, DebugBar\DataCollector\AssetProvider
    {
        // ...

        public function getWidgets()
        {
            return array(
                "database" => new DebugBar\Widget\SQLQueriesTab("inbox", "pdo")
            );
        }

        public function getAssets()
        {
            return array(
                'css' => 'widgets/sqlqueries/widget.css',
                'js' => 'widgets/sqlqueries/widget.js'
            );
        }
    }