# Rendering

Rendering is performed using the `DebugBar\JavascriptRenderer̀ class. It contains
all the useful functions to included the needed assets and generate a debug bar.

    $renderer = $debugbar->getJavascriptRenderer();

## Assets

The debug bar relies on some css and javascript files which needs to be included
into your webpage. This can be done in two ways:

 - Using `JavascriptRenderer::renderHead()` which will returns a string with
   the needed script and link tags
 - Using [Assetic](https://github.com/kriswallsmith/assetic) and 
   `JavascriptRenderer::getAsseticCollection()`

I would recommend using the second method as Assetic is a very powerful asset
manager but the first method is provided to quickly integrate the debug bar
into any projets.

You can define the base url of your assets using `setBaseUrl()`. This is needed
in 99% of cases. If you are using Assetic, you may have to change the base path
of the assets if you moved the folder. This can be done using `setBasePath()`.

Using `renderHead()`:

    <html>
        <head>
            ...
            <?php echo $renderer->renderHead() ?>
            ...
        </head>
        ...
    </html>

Using Assetic:

    list($cssCollection, $jsCollection) = $renderer->getAsseticCollection();

Note that you can only use the debug bar assets and manage the dependencies by yourself
using `$renderer->setIncludeVendors(false)`.

## The javascript object

The renderer will generate all the needed code for your debug bar. This means
initializing the DebugBar js object, adding tabs and indicators, defining a data map, etc...

Data collectors can provide their own controls when implementing the 
`DebugBar\DataCollector\Renderable` interface as explained in the Collecting Data chapter.

Thus in almost all cases, you should only have to use `render()` right away:

    <html>
        ...
        <body>
            <?php echo $renderer->render() ?>
        </body>
    </html>

This will print the initilization code for the toolbar and the dataset for the request.
When you are performing AJAX requests, you do not want to initialize a new toolbar but
add the dataset to the existing one. You can disable initialization using ̀false` as
the first argument of ̀render()`.

    <p>my ajax content</p>
    <?php echo $renderer->render(false) ?>

### Controlling object initialization

You can further control the initialization of the javascript object using `setInitialization()`.
It takes a bitwise value made out of the constants ̀INITIALIZE_CONSTRUCTOR` and `INITIALIZE_CONTROLS`.
The first one controls whether to initialize the variable (ie. `var debugbar = new DebugBar()`). The
second one whether to initialize all the controls (ie. adding tab and indicators as well as data mapping).

You can also control the class name of the object using `setJavascriptClass()` and the name of
the instance variable using `setVariableName()`.

Let's say you have subclassed `PhpDebugBar.DebugBar` in javascript to do your own initilization.
Your new object is called `MyDebugBar`.

    $renderer->setJavascriptClass("MyDebugBar");
    $renderer->setInitialization(JavascriptRenderer::INITIALIZE_CONSTRUCTOR);
    // ...
    echo $renderer->render();

This has the result of printing:

    <script type="text/javascript">
    var phpdebugbar = new MyDebugBar();
    phpdebugbar.addDataSet({ ... });
    </script>

Using `setInitialization(0)` will only render the addDataSet part.

### Defining controls

Controls can be manually added to the debug bar using `addControl($name, $options)`. You should read
the Javascript bar chapter before this section.

`$name` will be the name of your control and `$options` is a key/value pair array with these
possible values:

- *icon*: icon name
- *tooltip*: string
- *widget*: widget class name
- *map*: a property name from the data to map the control to
- *default*: a js string, default value of the data map

At least *icon* or *widget* are needed. If *widget* is specified, a tab will be created, otherwise
an indicator.

    $renderer->addControl('messages', array(
        "widget" => "PhpDebugBar.Widgets.MessagesWidget",
        "map" => "messages",
        "default" => "[]"
    ));
