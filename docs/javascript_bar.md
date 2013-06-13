# Javascript Bar

The default client side implementation of the debug bar is made
entirely in Javascript and is located in the *debugbar.js* file. 

It adds a bottom-anchored bar which can have tabs and indicators. 
The bar can be in an open or close state. When open, the tab panel is
visible.
An indicator is a piece of information displayed in the always-visible 
part of the bar.

The bar handles multiple datasets by displaying a select box
which allows you to switch between them.

The state of the bar (height, visibilty, active panel) can be saved
between requests (enabled in the standard bar).

Each panel is composed of a widget which is used to display the
data from a data collector. Some common widgets are provided in
the *widgets.js* file.

The `PhpDebugBar` namespace is used for all objects and the only
dependencies are *jQuery*, *jquery-drag* and *FontAwesome* (css).

The main class is `PhpDebugBar.DebugBar`. It provides the infrastructure
to manage tabs, indicators and datasets.

When initialized, the `DebugBar` class adds itself to the `<body>` of the
page. It is empty by default.

## Tabs and indicators

Controls (ie. tabs and indicators) are uniquely named. You can check if
a control exists using `isControl(name)`.

Tabs can be added using the `createTab(name, widget, title)` function.
The third argument is optional and will be computed from the name if not
provided.

    var debugbar = new PhpDebugBar.DebugBar();
    debugbar.createTab("messages", new PhpDebugBar.Widgets.MessagesWidget());

Indicators can be added using `createIndicator(name, icon, tooltip, position)`.
Only `name` is required in this case. `icon` should be the name of a FontAwesome
icon. `position` can either by *right* (default) or *left*.

    debugbar.createIndicator("time", "cogs", "Request duration");

You may have noticed that the data to use inside these controls is not
specified at the moment. Although it could be specified when initialized, it
is better to use data mapping to support dynamically changing the data set.

## Data mapping

To enable dynamically changing the data sets, we need to specify which values
should be feed into which controls. This can be done using `setDataMap(map)`
which takes as argument an object where properties are control names. Values
should be arrays where the first item is the property from the data set and 
the second a default value.

    debugbar.setDataMap({
        "messages": ["messages", []],
        "time": ["time.duration_str", "0ms"]
    });

You can notice that nested properties can also be accessed using the dot
notation.

In this mapping, `data["messages"]` will be fed to the *messages* tab
and `data["time"]["duration_str"]` will be fed to the *time* indicator.

Note: you can append mapping info using `addDataMap(map)`

## Datasets

Although you shouldn't have to do anything regarding managing datasets,
it is interesting to know a few functions related to them.

`addDataSet(data, id)` adds a dataset to the bar. The select box that
allows to swtich between sets is only displayed if more than one are added.
`id` is optional and will be auto-generated if not specified.

`showDataSet(id)` allows you to switch to a specific dataset.

## Widgets

A widget is a javascript object which must contain at least an `element`
property. `element`'s value will be appended to the tab panel.

Widgets should provide a `setData()` function so they can be used with
the data mapper.

    var MyWidget = function() {
        this.element = $('<div class="mywidget" />');
    };

    MyWidget.prototype.setData = function(text) {
        this.element.text(text);
    };

    // ----

    debugbar.createTab("mytab", new MyWidget());
    debugbar.addDataMap({"mytab": ["mydata", ""]});

Widgets for bundled data collectors are included as well as more generic
widgets that you can build on top of. They are located in *widgets.js* in
the `PhpDebugBar.Widgets` namespace.

Generic widgets:

 - `ListWidget`: renders an array as a UL list
 - `KVListWidget`: renders a hash as a DL list
 - `VariablesListWidget`: an extension of `KVListWidget` to display a list of variables
 - `IFrameWidget`: renders an iframe

Data collectors related widgets:

 - `MessagesWidget`: for the `MessagesCollector`
 - `TimelineWidget`: for the `TimeDataCollector`
