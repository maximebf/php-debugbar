
var index = lunr(function () {
    this.field('body');
    this.ref('url');
});

var documentTitles = {};



documentTitles["/docs/readme.html#php-debug-bar"] = "PHP Debug Bar";
index.add({
    url: "/docs/readme.html#php-debug-bar",
    title: "PHP Debug Bar",
    body: "# PHP Debug Bar  Displays a debug bar in the browser with information from php. No more `var_dump()` in your code!  ![Screenshot](https://raw.github.com/maximebf/php-debugbar/master/docs/screenshot.png)  **Features:**   - Generic debug bar with no other dependencies  - Easy to integrate with any project  - Clean, fast and easy to use interface  - Handles AJAX request  - Includes generic data collectors and collectors for well known libraries  - The client side bar is 100% coded in javascript  - Easily create your own collectors and their associated view in the bar  - [Very well documented](http://phpdebugbar.com/docs)  Includes collectors for:    - [PDO](http://php.net/manual/en/book.pdo.php)   - [CacheCache](http://maximebf.github.io/CacheCache/)   - [Doctrine](http://doctrine-project.org)   - [Monolog](https://github.com/Seldaek/monolog)   - [Propel](http://propelorm.org/)   - [Slim](http://slimframework.com)   - [Swift Mailer](http://swiftmailer.org/)   - [Twig](http://twig.sensiolabs.org/)  Checkout the [demo](https://github.com/maximebf/php-debugbar/tree/master/demo) for examples and [phpdebugbar.com](http://phpdebugbar.com) for a live example.  "
});

documentTitles["/docs/readme.html#installation"] = "Installation";
index.add({
    url: "/docs/readme.html#installation",
    title: "Installation",
    body: "## Installation  The easiest way to install DebugBar is using [Composer](https://github.com/composer/composer) with the following requirement:      {         \&quot;require\&quot;: {             \&quot;maximebf/debugbar\&quot;: \&quot;&gt;=1.0.0\&quot;         }     }  Alternatively, you can [download the archive](https://github.com/maximebf/php-debugbar/zipball/master)  and add the src/ folder to PHP's include path:      set_include_path('/path/to/src' . PATH_SEPARATOR . get_include_path());  DebugBar does not provide an autoloader but follows the [PSR-0 convention](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).   You can use the following snippet to autoload DebugBar classes:      spl_autoload_register(function($className) {         if (substr($className, 0, 8) === 'DebugBar') {             $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';             require_once $filename;         }     });  "
});

documentTitles["/docs/readme.html#quick-start"] = "Quick start";
index.add({
    url: "/docs/readme.html#quick-start",
    title: "Quick start",
    body: "## Quick start  DebugBar is very easy to use and you can add it to any of your projets in no time. The easiest way is using the `render()` functions      &lt;?php     use DebugBar\StandardDebugBar;     use DebugBar\JavascriptRenderer;      $debugbar = new StandardDebugBar();     $debugbarRenderer = $debugbar-&gt;getJavascriptRenderer();      $debugbar[\&quot;messages\&quot;]-&gt;addMessage(\&quot;hello world!\&quot;);     ?&gt;     &lt;html&gt;         &lt;head&gt;             &lt;?php echo $debugbarRenderer-&gt;renderHead() ?&gt;         &lt;/head&gt;         &lt;body&gt;             ...             &lt;?php echo $debugbarRenderer-&gt;render() ?&gt;         &lt;/body&gt;     &lt;/html&gt;  The DebugBar uses DataCollectors to collect data from your PHP code. Some of them are automated but others are manual. Use the `DebugBar` like an array where keys are the collector names. In our previous example, we add a message to the `MessagesCollector`:      $debugbar[\&quot;messages\&quot;]-&gt;addMessage(\&quot;hello world!\&quot;);  `StandardDebugBar` activates the following collectors:   - `MemoryCollector` (*memory*)  - `MessagesCollector` (*messages*)  - `PhpInfoCollector` (*php*)  - `RequestDataCollector` (*request*)  - `TimeDataCollector` (*time*)  - `ExceptionsCollector` (*exceptions*)  Learn more about DebugBar in the [docs](http://phpdebugbar.com/docs). "
});



documentTitles["/docs/readme.html#php-debug-bar"] = "PHP Debug Bar";
index.add({
    url: "/docs/readme.html#php-debug-bar",
    title: "PHP Debug Bar",
    body: "# PHP Debug Bar  Displays a debug bar in the browser with information from php. No more `var_dump()` in your code!  ![Screenshot](https://raw.github.com/maximebf/php-debugbar/master/docs/screenshot.png)  **Features:**   - Generic debug bar with no other dependencies  - Easy to integrate with any project  - Clean, fast and easy to use interface  - Handles AJAX request  - Includes generic data collectors and collectors for well known libraries  - The client side bar is 100% coded in javascript  - Easily create your own collectors and their associated view in the bar  - [Very well documented](http://phpdebugbar.com/docs)  Includes collectors for:    - [PDO](http://php.net/manual/en/book.pdo.php)   - [CacheCache](http://maximebf.github.io/CacheCache/)   - [Doctrine](http://doctrine-project.org)   - [Monolog](https://github.com/Seldaek/monolog)   - [Propel](http://propelorm.org/)   - [Slim](http://slimframework.com)   - [Swift Mailer](http://swiftmailer.org/)   - [Twig](http://twig.sensiolabs.org/)  Checkout the [demo](https://github.com/maximebf/php-debugbar/tree/master/demo) for examples and [phpdebugbar.com](http://phpdebugbar.com) for a live example.  "
});

documentTitles["/docs/readme.html#installation"] = "Installation";
index.add({
    url: "/docs/readme.html#installation",
    title: "Installation",
    body: "## Installation  The easiest way to install DebugBar is using [Composer](https://github.com/composer/composer) with the following requirement:      {         \&quot;require\&quot;: {             \&quot;maximebf/debugbar\&quot;: \&quot;&gt;=1.0.0\&quot;         }     }  Alternatively, you can [download the archive](https://github.com/maximebf/php-debugbar/zipball/master)  and add the src/ folder to PHP's include path:      set_include_path('/path/to/src' . PATH_SEPARATOR . get_include_path());  DebugBar does not provide an autoloader but follows the [PSR-0 convention](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).   You can use the following snippet to autoload DebugBar classes:      spl_autoload_register(function($className) {         if (substr($className, 0, 8) === 'DebugBar') {             $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';             require_once $filename;         }     });  "
});

documentTitles["/docs/readme.html#quick-start"] = "Quick start";
index.add({
    url: "/docs/readme.html#quick-start",
    title: "Quick start",
    body: "## Quick start  DebugBar is very easy to use and you can add it to any of your projets in no time. The easiest way is using the `render()` functions      &lt;?php     use DebugBar\StandardDebugBar;     use DebugBar\JavascriptRenderer;      $debugbar = new StandardDebugBar();     $debugbarRenderer = $debugbar-&gt;getJavascriptRenderer();      $debugbar[\&quot;messages\&quot;]-&gt;addMessage(\&quot;hello world!\&quot;);     ?&gt;     &lt;html&gt;         &lt;head&gt;             &lt;?php echo $debugbarRenderer-&gt;renderHead() ?&gt;         &lt;/head&gt;         &lt;body&gt;             ...             &lt;?php echo $debugbarRenderer-&gt;render() ?&gt;         &lt;/body&gt;     &lt;/html&gt;  The DebugBar uses DataCollectors to collect data from your PHP code. Some of them are automated but others are manual. Use the `DebugBar` like an array where keys are the collector names. In our previous example, we add a message to the `MessagesCollector`:      $debugbar[\&quot;messages\&quot;]-&gt;addMessage(\&quot;hello world!\&quot;);  `StandardDebugBar` activates the following collectors:   - `MemoryCollector` (*memory*)  - `MessagesCollector` (*messages*)  - `PhpInfoCollector` (*php*)  - `RequestDataCollector` (*request*)  - `TimeDataCollector` (*time*)  - `ExceptionsCollector` (*exceptions*)  Learn more about DebugBar in the [docs](http://phpdebugbar.com/docs). "
});



documentTitles["/docs/data-collectors.html#collecting-data"] = "Collecting Data";
index.add({
    url: "/docs/data-collectors.html#collecting-data",
    title: "Collecting Data",
    body: "# Collecting Data  "
});

documentTitles["/docs/data-collectors.html#using-collectors"] = "Using collectors";
index.add({
    url: "/docs/data-collectors.html#using-collectors",
    title: "Using collectors",
    body: "## Using collectors  Collectors can be added to your debug bar using `addCollector()`.       $debugbar = new DebugBar();     $debugbar-&gt;addCollector(new DataCollector\RequestDataCollector());  Each collector as a unique name as defined by its `getName()` method. You can  access collectors using `getCollector($name)`.      $debugbar-&gt;addCollector(new DataCollector\MessagesCollector());     $debugbar-&gt;getCollector('messages')-&gt;addMessage(\&quot;foobar\&quot;);     // or:     $debugbar['messages']-&gt;addMessage(\&quot;foobar\&quot;);  Data will be collected from them when the debug bar is rendered. You can however collect the data earlier using `collect()`.      $debugbar-&gt;collect();  "
});

documentTitles["/docs/data-collectors.html#creating-collectors"] = "Creating collectors";
index.add({
    url: "/docs/data-collectors.html#creating-collectors",
    title: "Creating collectors",
    body: "## Creating collectors  Collectors must implement the `DebugBar\DataCollector\DataCollectorInterface`. They may subclass `DebugBar\DataCollector\DataCollector` which provides utility methods.  Collectors must provide a `getName()` function returning their unique name and a `collect()` function returning some json-encodable data. The latter will be called at the same time the `DebugBar::collect()` method is called.      class MyDataCollector extends DebugBar\DataCollector\DataCollector     {         public function collect()         {             return array(\&quot;uniqid\&quot; =&gt; uniqid());         }          public function getName()         {             return 'mycollector';         }     }      $debugbar-&gt;addCollector(new MyDataCollector());  This however won't show anything in the debug bar as no information are provided on how to display these data. You could do that manually as you'll see in later chapter or implement the `DebugBar\DataSource\Renderable` interface.   To implement it, you must define a `getWidgets()` function which returns an array of key/value pairs where key are control names and values control options as defined in `JavascriptRenderer::addControl($name, $options)` (see Rendering chapter).      class MyDataCollector extends DebugBar\DataCollector\DataCollector implements DebugBar\DataCollector\Renderable     {         // ...          public function getWidgets()         {             return array(                 \&quot;mycollector\&quot; =&gt; array(                     \&quot;icon\&quot; =&gt; \&quot;cogs\&quot;,                     \&quot;tooltip\&quot; =&gt; \&quot;uniqid()\&quot;,                     \&quot;map\&quot; =&gt; \&quot;uniqid\&quot;,                     \&quot;default\&quot; =&gt; \&quot;''\&quot;                 )             );         }     }  This will have the result of adding a new indicator to the debug bar. "
});



documentTitles["/docs/rendering.html#rendering"] = "Rendering";
index.add({
    url: "/docs/rendering.html#rendering",
    title: "Rendering",
    body: "# Rendering  Rendering is performed using the `DebugBar\JavascriptRenderer̀ class. It contains all the useful functions to included the needed assets and generate a debug bar.      $renderer = $debugbar-&gt;getJavascriptRenderer();  "
});

documentTitles["/docs/rendering.html#assets"] = "Assets";
index.add({
    url: "/docs/rendering.html#assets",
    title: "Assets",
    body: "## Assets  The debug bar relies on some css and javascript files which needs to be included into your webpage. They are located in the *src/DebugBar/Resources* folder.  This can be done in three ways:   - Using `JavascriptRenderer::renderHead()` which will returns a string with    the needed script and link tags  - Using [Assetic](https://github.com/kriswallsmith/assetic) and     `JavascriptRenderer::getAsseticCollection()`  - Dumping the assets yourself using `JavascriptRenderer::dumpCssAssets()` and     `JavascriptRenderer::dumpJsAssets()`  I would recommend using the second method as Assetic is a very powerful asset manager but the other methods are provided to quickly integrate the debug bar into any projets.  You can define the base url of your assets using `setBaseUrl()`. This is needed in 99% of cases.  Using `renderHead()`:      &lt;html&gt;         &lt;head&gt;             ...             &lt;?php echo $renderer-&gt;renderHead() ?&gt;             ...         &lt;/head&gt;         ...     &lt;/html&gt;  Using Assetic:      list($cssCollection, $jsCollection) = $renderer-&gt;getAsseticCollection();  Dumping the assets:      header('Content-Type', 'text/javascript');     $renderer-&gt;dumpJsAssets();  Note that you can only use the debug bar assets and manage the dependencies by yourself using `$renderer-&gt;setIncludeVendors(false)`.  "
});

documentTitles["/docs/rendering.html#the-javascript-object"] = "The javascript object";
index.add({
    url: "/docs/rendering.html#the-javascript-object",
    title: "The javascript object",
    body: "## The javascript object  The renderer will generate all the needed code for your debug bar. This means initializing the DebugBar js object, adding tabs and indicators, defining a data map, etc...  Data collectors can provide their own controls when implementing the  `DebugBar\DataCollector\Renderable` interface as explained in the Collecting Data chapter.  Thus in almost all cases, you should only have to use `render()` right away:      &lt;html&gt;         ...         &lt;body&gt;             &lt;?php echo $renderer-&gt;render() ?&gt;         &lt;/body&gt;     &lt;/html&gt;  This will print the initilization code for the toolbar and the dataset for the request. When you are performing AJAX requests, you do not want to initialize a new toolbar but add the dataset to the existing one. You can disable initialization using ̀false` as the first argument of ̀render()`.      &lt;p&gt;my ajax content&lt;/p&gt;     &lt;?php echo $renderer-&gt;render(false) ?&gt;  "
});

documentTitles["/docs/rendering.html#controlling-object-initialization"] = "Controlling object initialization";
index.add({
    url: "/docs/rendering.html#controlling-object-initialization",
    title: "Controlling object initialization",
    body: "### Controlling object initialization  You can further control the initialization of the javascript object using `setInitialization()`. It takes a bitwise value made out of the constants ̀INITIALIZE_CONSTRUCTOR` and `INITIALIZE_CONTROLS`. The first one controls whether to initialize the variable (ie. `var debugbar = new DebugBar()`). The second one whether to initialize all the controls (ie. adding tab and indicators as well as data mapping).  You can also control the class name of the object using `setJavascriptClass()` and the name of the instance variable using `setVariableName()`.  Let's say you have subclassed `PhpDebugBar.DebugBar` in javascript to do your own initilization. Your new object is called `MyDebugBar`.      $renderer-&gt;setJavascriptClass(\&quot;MyDebugBar\&quot;);     $renderer-&gt;setInitialization(JavascriptRenderer::INITIALIZE_CONSTRUCTOR);     // ...     echo $renderer-&gt;render();  This has the result of printing:      &lt;script type=\&quot;text/javascript\&quot;&gt;     var phpdebugbar = new MyDebugBar();     phpdebugbar.addDataSet({ ... });     &lt;/script&gt;  Using `setInitialization(0)` will only render the addDataSet part.  "
});

documentTitles["/docs/rendering.html#defining-controls"] = "Defining controls";
index.add({
    url: "/docs/rendering.html#defining-controls",
    title: "Defining controls",
    body: "### Defining controls  Controls can be manually added to the debug bar using `addControl($name, $options)`. You should read the Javascript bar chapter before this section.  `$name` will be the name of your control and `$options` is a key/value pair array with these possible values:  - *icon*: icon name - *tooltip*: string - *widget*: widget class name - *map*: a property name from the data to map the control to - *default*: a js string, default value of the data map - *tab*: class name of the tab object (to use a custom tab object) - *indicator*: class name of the indicator object (to use a custom indicator object)  At least *icon* or *widget* are needed (unless *tab* or *indicator* are specified). If *widget* is  specified, a tab will be created, otherwise an indicator. Any other options is also passed to the tab or indicator.      $renderer-&gt;addControl('messages', array(         \&quot;widget\&quot; =&gt; \&quot;PhpDebugBar.Widgets.MessagesWidget\&quot;,         \&quot;map\&quot; =&gt; \&quot;messages\&quot;,         \&quot;default\&quot; =&gt; \&quot;[]\&quot;     ));  You can disable a control using `disableControl($name)` and ignore any controls provided by a collector using `ignoreCollector($name)`."
});



documentTitles["/docs/javascript-bar.html#javascript-bar"] = "Javascript Bar";
index.add({
    url: "/docs/javascript-bar.html#javascript-bar",
    title: "Javascript Bar",
    body: "# Javascript Bar  The default client side implementation of the debug bar is made entirely in Javascript and is located in the *debugbar.js* file.   It adds a bottom-anchored bar which can have tabs and indicators.  The bar can be in an open or close state. When open, the tab panel is visible. An indicator is a piece of information displayed in the always-visible  part of the bar.  The bar handles multiple datasets by displaying a select box which allows you to switch between them.  The state of the bar (height, visibilty, active panel) can be saved between requests (enabled in the standard bar).  Each panel is composed of a widget which is used to display the data from a data collector. Some common widgets are provided in the *widgets.js* file.  The `PhpDebugBar` namespace is used for all objects and the only dependencies are *jQuery* and *FontAwesome* (css). *FontAwesome* is optional but is used to add nice icons!  The main class is `PhpDebugBar.DebugBar`. It provides the infrastructure to manage tabs, indicators and datasets.  When initialized, the `DebugBar` class adds itself to the `&lt;body&gt;` of the page. It is empty by default.  "
});

documentTitles["/docs/javascript-bar.html#tabs-and-indicators"] = "Tabs and indicators";
index.add({
    url: "/docs/javascript-bar.html#tabs-and-indicators",
    title: "Tabs and indicators",
    body: "## Tabs and indicators  Controls (ie. tabs and indicators) are uniquely named. You can check if a control exists using `isControl(name)`.  Tabs can be added using the `createTab(name, widget, title)` function. The third argument is optional and will be computed from the name if not provided.      var debugbar = new PhpDebugBar.DebugBar();     debugbar.createTab(\&quot;messages\&quot;, new PhpDebugBar.Widgets.MessagesWidget());  Indicators can be added using `createIndicator(name, icon, tooltip, position)`. Only `name` is required in this case. `icon` should be the name of a FontAwesome icon. `position` can either be *right* (default) or *left*.      debugbar.createIndicator(\&quot;time\&quot;, \&quot;cogs\&quot;, \&quot;Request duration\&quot;);  You may have noticed that the data to use inside these controls is not specified at the moment. Although it could be specified when initialized, it is better to use data mapping to support dynamically changing the data set.  "
});

documentTitles["/docs/javascript-bar.html#data-mapping"] = "Data mapping";
index.add({
    url: "/docs/javascript-bar.html#data-mapping",
    title: "Data mapping",
    body: "## Data mapping  To enable dynamically changing the data sets, we need to specify which values should be feed into which controls. This can be done using `setDataMap(map)` which takes as argument an object where properties are control names. Values should be arrays where the first item is the property from the data set and  the second a default value.      debugbar.setDataMap({         \&quot;messages\&quot;: [\&quot;messages\&quot;, []],         \&quot;time\&quot;: [\&quot;time.duration_str\&quot;, \&quot;0ms\&quot;]     });  You can notice that nested properties can also be accessed using the dot notation.  In this mapping, `data[\&quot;messages\&quot;]` will be fed to the *messages* tab and `data[\&quot;time\&quot;][\&quot;duration_str\&quot;]` will be fed to the *time* indicator.  Note: you can append mapping info using `addDataMap(map)`  "
});

documentTitles["/docs/javascript-bar.html#datasets"] = "Datasets";
index.add({
    url: "/docs/javascript-bar.html#datasets",
    title: "Datasets",
    body: "## Datasets  Although you shouldn't have to do anything regarding managing datasets, it is interesting to know a few functions related to them.  `addDataSet(data, id)` adds a dataset to the bar. The select box that allows to swtich between sets is only displayed if more than one are added. `id` is optional and will be auto-generated if not specified.  `showDataSet(id)` allows you to switch to a specific dataset.  "
});

documentTitles["/docs/javascript-bar.html#widgets"] = "Widgets";
index.add({
    url: "/docs/javascript-bar.html#widgets",
    title: "Widgets",
    body: "## Widgets  Widgets should inherit from the `PhpDebugBar.Widget` class which is used as the base of every visual component in the debug bar.  New widgets can be created using `extend()`:      var MyWidget = PhpDebugBar.Widget.extend({         // class properties     });  The Widget class defines a `set(attr, value)` function which can be used to set the value of attributes.  Using `bindAttr(attr, callback)`, you can trigger a callback everytime the value of the attribute is changed. `callback` can also be a `jQuery` object and in that case it will use the `text()` function to fill the element.  Widgets should define a `render()` function which initializes the widget elements.  `initiliaze(options)` will always be called after the constructor.      var MyWidget = PhpDebugBar.Widget.extend({          tagName: 'div', // optional as 'div' is the default          className: 'mywidget',          render: function() {             this.bindAttr('data', this.$el);         }      });      // ----      debugbar.createTab(\&quot;mytab\&quot;, new MyWidget());     debugbar.addDataMap({\&quot;mytab\&quot;: [\&quot;mydata\&quot;, \&quot;\&quot;]});  Widgets for bundled data collectors are included as well as more generic widgets that you can build on top of. They are located in *widgets.js* in the `PhpDebugBar.Widgets` namespace.  Generic widgets:   - `ListWidget`: renders an array as a UL list  - `KVListWidget`: renders a hash as a DL list  - `VariablesListWidget`: an extension of `KVListWidget` to display a list of variables  - `IFrameWidget`: renders an iframe  Data collectors related widgets:   - `MessagesWidget`: for the `MessagesCollector`  - `TimelineWidget`: for the `TimeDataCollector`  - `ExceptionWidget`: for the `ExceptionCollector`  - `SQLQueriesWidget`: for the `PDOCollector`  - `TemplatesWidget`: for the `TwigCollector`  "
});

documentTitles["/docs/javascript-bar.html#custom-tabs-and-indicators"] = "Custom tabs and indicators";
index.add({
    url: "/docs/javascript-bar.html#custom-tabs-and-indicators",
    title: "Custom tabs and indicators",
    body: "## Custom tabs and indicators  Behind the scene, `createTab()` and `createIndicator()` use `addTab(name, tab)` and  `addIndicator(name, indicator)`. Tabs are objects of type `PhpDebugBar.DebugBar.Tab`  and indicators of type `PhpDebugBar.DebugBar.Indicator`. These classes subclass  `PhpDebugBar.Widget` which makes it easy to create custom tabs or indicators.      var LinkIndicator = PhpDebugBar.DebugBar.Indicator.extend({          tagName: 'a',          render: function() {             LinkIndicator.__super__.render.apply(this);             this.bindAttr('href', function(href) {                 this.$el.attr('href', href);             });         }      });      // ----      debugbar.addIndicator('phpdoc', new LinkIndicator({ href: 'http://doc.php.com', title: 'PHP doc' })); "
});



documentTitles["/docs/base-collectors.html#base-collectors"] = "Base collectors";
index.add({
    url: "/docs/base-collectors.html#base-collectors",
    title: "Base collectors",
    body: "# Base collectors  Collectors provided in the `DebugBar\DataCollector` namespace.  "
});

documentTitles["/docs/base-collectors.html#messages"] = "Messages";
index.add({
    url: "/docs/base-collectors.html#messages",
    title: "Messages",
    body: "## Messages  Provides a way to log messages (compotible with [PSR-3 logger](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md)).      $debugbar-&gt;addCollector(new DebugBar\DataCollector\MessagesCollector());     $debugbar['messages']-&gt;info('hello world');  You can have multiple messages collector by naming them:      $debugbar-&gt;addCollector(new MessagesCollector('io_ops'));     $debugbar['io_ops']-&gt;info('opening files');  You can aggregate messages collector into other to have a unified view:      $debugbar['messages']-&gt;aggregate($debugbar['io_ops']);  If you don't want to create a standalone tab in the debug bar but still be able to log messages from a different collector, you don't have to add the collector to the debug bar:      $debugbar['messages']-&gt;aggregate(new MessagesCollector('io_ops'));  "
});

documentTitles["/docs/base-collectors.html#timedata"] = "TimeData";
index.add({
    url: "/docs/base-collectors.html#timedata",
    title: "TimeData",
    body: "## TimeData  Provides a way to log total execution time as well as taking \&quot;measures\&quot; (ie. measure the execution time of a particular operation).      $debugbar-&gt;addCollector(new DebugBar\DataCollector\TimeDataCollector());          $debugbar['time']-&gt;startMeasure('longop', 'My long operation');     sleep(2);     $debugbar['time']-&gt;stopMeasure('longop');      $debugbar['time']-&gt;measure('My long operation', function() {         sleep(2);     });  Displays the measures on a timeline  "
});

documentTitles["/docs/base-collectors.html#exceptions"] = "Exceptions";
index.add({
    url: "/docs/base-collectors.html#exceptions",
    title: "Exceptions",
    body: "## Exceptions  Display exceptions      $debugbar-&gt;addCollector(new DebugBar\DataCollector\ExceptionsCollector());      try {         throw new Exception('foobar');     } catch (Exception $e) {         $debugbar['exceptions']-&gt;addException($e);     }  "
});

documentTitles["/docs/base-collectors.html#pdo"] = "PDO";
index.add({
    url: "/docs/base-collectors.html#pdo",
    title: "PDO",
    body: "## PDO  Logs SQL queries. You need to wrap your `PDO` object into a `DebugBar\DataCollector\PDO\TraceablePDO` object.      $pdo = new DebugBar\DataCollector\PDO\TraceablePDO(new PDO('sqlite::memory:'));     $debugbar-&gt;addCollector(new DebugBar\DataCollector\PDO\PDOCollector($pdo));  "
});

documentTitles["/docs/base-collectors.html#requestdatacollector"] = "RequestDataCollector";
index.add({
    url: "/docs/base-collectors.html#requestdatacollector",
    title: "RequestDataCollector",
    body: "## RequestDataCollector  Collects the data of PHP's global variables      $debugbar-&gt;addCollector(new DebugBar\DataCollector\RequestDataCollector());  "
});

documentTitles["/docs/base-collectors.html#aggregatedcollector"] = "AggregatedCollector";
index.add({
    url: "/docs/base-collectors.html#aggregatedcollector",
    title: "AggregatedCollector",
    body: "## AggregatedCollector  Aggregates multiple collectors. Do not provide any widgets, you have to add your own controls.      $debugbar-&gt;addCollector(new DebugBar\DataCollector\AggregatedCollector('all_messages', 'messages', 'time'));     $debugbar['all_messages']-&gt;addCollector($debugbar['messages']);     $debugbar['all_messages']-&gt;addCollector(new MessagesCollector('mails'));     $debugbar['all_messages']['mails']-&gt;addMessage('sending mail');      $renderer = $debugbar-&gt;getJavascriptRenderer();     $renderer-&gt;addControl('all_messages', array(         'widget' =&gt; 'PhpDebugBar.Widgets.MessagesWidget',         'map' =&gt; 'all_messages',         'default' =&gt; '[]';     ));  "
});

documentTitles["/docs/base-collectors.html#others"] = "Others";
index.add({
    url: "/docs/base-collectors.html#others",
    title: "Others",
    body: "## Others  Misc collectors which you can just register:   - `MemoryCollector` (*memory*): Display memory usage  - `PhpInfoCollector` (*php*): PHP version number "
});



documentTitles["/docs/bridge-collectors.html#bridge-collectors"] = "Bridge collectors";
index.add({
    url: "/docs/bridge-collectors.html#bridge-collectors",
    title: "Bridge collectors",
    body: "# Bridge collectors  DebugBar comes with some \&quot;bridge\&quot; collectors. This collectors provides a way to integrate other projets with the DebugBar.  "
});

documentTitles["/docs/bridge-collectors.html#cachecache"] = "CacheCache";
index.add({
    url: "/docs/bridge-collectors.html#cachecache",
    title: "CacheCache",
    body: "## CacheCache  http://maximebf.github.io/CacheCache/  Displays cache operations using `DebugBar\Bridge\CacheCacheCollector`      $cache = new CacheCache\Cache(new CacheCache\Backends\Memory());     $debugbar-&gt;addCollector(new DebugBar\Bridge\CacheCacheCollector($cache));  CacheCache uses [Monolog](https://github.com/Seldaek/monolog) for logging,  thus it is required to collect data.  `CacheCacheCollector` subclasses `MonologCollector`, thus it can be  [aggregated in the messages view](base-collectors.html#messages).  "
});

documentTitles["/docs/bridge-collectors.html#doctrine"] = "Doctrine";
index.add({
    url: "/docs/bridge-collectors.html#doctrine",
    title: "Doctrine",
    body: "## Doctrine  http://doctrine-project.org  Displays sql queries into an SQL queries view using `DebugBar\Bridge\DoctrineCollector`.  You will need to set a `Doctrine\DBAL\Logging\DebugStack` logger on your connection.      $debugStack = new Doctrine\DBAL\Logging\DebugStack();     $entityManager-&gt;getConnection()-&gt;getConfiguration()-&gt;setSQLLogger($debugStack);     $debugbar-&gt;addCollector(new DebugBar\Bridge\DoctrineCollector($debugStack));  `DoctrineCollector` also accepts an `Doctrine\ORM\EntityManager` as argument provided the `SQLLogger` is a ̀DebugStack`.  "
});

documentTitles["/docs/bridge-collectors.html#monolog"] = "Monolog";
index.add({
    url: "/docs/bridge-collectors.html#monolog",
    title: "Monolog",
    body: "## Monolog  https://github.com/Seldaek/monolog  Integrates Monolog messages into a message view using `DebugBar\Bridge\MonologCollector`.      $logger = new Monolog\Logger('mylogger');     $debugbar-&gt;addCollector(new DebugBar\Bridge\MonologCollector($logger));  Note that multiple logger can be collected:      $debugbar['monolog']-&gt;addLogger($logger);  `MonologCollector` can be [aggregated](base-collectors.html#messages) into the `MessagesCollector`.  "
});

documentTitles["/docs/bridge-collectors.html#propel"] = "Propel";
index.add({
    url: "/docs/bridge-collectors.html#propel",
    title: "Propel",
    body: "## Propel  http://propelorm.org/  Displays propel queries into an SQL queries view using `DebugBar\Bridge\PropelCollector`.  You will need to activate Propel debug mode.      // before Propel::init()     $debugbar-&gt;addCollector(new DebugBar\Bridge\PropelCollector());      Propel::init('/path/to/config');      // after Propel::init()     // not mandatory if you set config options by yourself     DebugBar\Bridge\PropelCollector::enablePropelProfiling();  Queries can be collected on a single connection by providing the `PropelPDO` object to the `PropelCollector` as first argument.  "
});

documentTitles["/docs/bridge-collectors.html#slim"] = "Slim";
index.add({
    url: "/docs/bridge-collectors.html#slim",
    title: "Slim",
    body: "## Slim  http://slimframework.com  Displays message from the Slim logger into a message view using `DebugBar\Bridge\SlimCollector`.      $app = new Slim\Slim();     $debugbar-&gt;addCollector(new DebugBar\Bridge\SlimCollector($app));  "
});

documentTitles["/docs/bridge-collectors.html#swift-mailer"] = "Swift Mailer";
index.add({
    url: "/docs/bridge-collectors.html#swift-mailer",
    title: "Swift Mailer",
    body: "## Swift Mailer  http://swiftmailer.org/  Display log messages and sent mail using `DebugBar\Bridge\SwiftMailer\SwiftLogCollector` and `DebugBar\Bridge\SwiftMailer\SwiftMailCollector`.      $mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());     $debugbar['messages']-&gt;aggregate(new DebugBar\Bridge\SwiftMailer\SwiftLogCollector($mailer));     $debugbar-&gt;addCollector(new DebugBar\Bridge\SwiftMailer\SwiftMailCollector($mailer));  "
});

documentTitles["/docs/bridge-collectors.html#twig"] = "Twig";
index.add({
    url: "/docs/bridge-collectors.html#twig",
    title: "Twig",
    body: "## Twig  http://twig.sensiolabs.org/  Collects info about rendered templates using `DebugBar\Bridge\Twig\TwigCollector`.  You need to wrap your `Twig_Environment` object into a `DebugBar\Bridge\Twig\TraceableTwigEnvironment` object.      $loader = new Twig_Loader_Filesystem('.');     $env = new DebugBar\Bridge\Twig\TraceableTwigEnvironment(new Twig_Environment($loader));     $debugbar-&gt;addCollector(new DebugBar\Bridge\Twig\TwigDataCollector($env));  You can provide a `DebugBar\DataCollector\TimeDataCollector` as the second argument of `TraceableTwigEnvironment` so render operation can be measured. "
});


