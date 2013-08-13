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
