if (typeof(PhpDebugBar) == 'undefined') {
    // namespace
    var PhpDebugBar = {};
}

if (typeof(localStorage) == 'undefined') {
    // provide mock localStorage object for dumb browsers
    var localStorage = {
        setItem: function(key, value) {},
        getItem: function(key) { return null; }
    };
}

/**
 * DebugBar
 *
 * Creates a bar that appends itself to the body of your page
 * and sticks to the bottom.
 *
 * The bar can be customized by adding tabs and indicators.
 * A data map is used to fill those controls with data provided
 * from datasets.
 * 
 * @constructor
 */
PhpDebugBar.DebugBar = (function($) {

    /**
     * Returns the value from an object property.
     * Using dots in the key, it is possible to retreive nested property values
     * 
     * @param {Object} dict
     * @param {String} key
     * @param {Object} default_value
     * @return {Object}
     */
    function getDictValue(dict, key, default_value) {
        var d = dict, parts = key.split('.');
        for (var i = 0; i < parts.length; i++) {
            if (!d[parts[i]]) {
                return default_value;
            }
            d = d[parts[i]];
        }
        return d;
    }

    // ------------------------------------------------------------------

    /**
     * Tab
     * 
     * A tab is composed of a tab label which is always visible and
     * a tab panel which is visible only when the tab is active.
     *
     * The panel must contain a widget. A widget is an object which has
     * an element property containing something appendable to a jQuery object.
     *
     * @this {Tab}
     * @constructor
     * @param {String} title
     * @param {Object} widget
     */
    var Tab = function(title, widget) {
        this.tab = $('<a href="javascript:" class="phpdebugbar-tab" />').text(title);
        this.panel = $('<div class="phpdebugbar-panel" />');
        this.replaceWidget(widget);
    };

    /**
     * Sets the title of the tab
     *
     * @this {Tab}
     * @param {String} text
     */
    Tab.prototype.setTitle = function(text) {
        this.tab.text(text);
    };

    /**
     * Replaces the widget inside the panel
     * 
     * @this {Tab}
     * @param {Object} new_widget
     */
    Tab.prototype.replaceWidget = function(new_widget) {
        this.panel.empty().append(new_widget.element);
        this.widget = new_widget;
    };

    // ------------------------------------------------------------------

    /**
     * Indicator
     *
     * An indicator is a text and an icon to display single value information
     * right inside the always visible part of the debug bar
     * 
     * @this {Indicator}
     * @constructor
     * @param {String} icon
     * @param {String} tooltip
     * @param {String} position "right" or "left", default is "right"
     */
    var Indicator = function(icon, tooltip, position) {
        if (!position) {
            position = 'right'
        }

        this.position = position;
        this.element = $('<span class="phpdebugbar-indicator" />').css('float', position);
        this.label = $('<span class="text" />').appendTo(this.element);

        if (icon) {
            $('<i class="icon-' + icon + '" />').insertBefore(this.label);
        }
        if (tooltip) {
            this.element.append($('<span class="tooltip" />').text(tooltip));
        }
    };

    /**
     * Sets the text of the indicator
     *
     * @this {Indicator}
     * @param {String} text
     */
    Indicator.prototype.setText = function(text) {
        this.element.find('.text').text(text);
    };

    /**
     * Sets the tooltip of the indicator
     *
     * @this {Indicator}
     * @param {String} text
     */
    Indicator.prototype.setTooltip = function(text) {
        this.element.find('.tooltip').text(text);
    };

    // ------------------------------------------------------------------


    /**
     * DebugBar
     *
     * @this {DebugBar}
     * @constructor
     */
    var DebugBar = function() {
        this.controls = {};
        this.dataMap = {};
        this.datasets = {};
        this.initUI();
        this.init();
    };

    /**
     * Initialiazes the UI
     *
     * @this {DebugBar}
     */
    DebugBar.prototype.initUI = function() {
        var self = this;
        this.element = $('<div class="phpdebugbar" />').appendTo('body');
        this.header = $('<div class="phpdebugbar-header" />').appendTo(this.element);
        this.body = $('<div class="phpdebugbar-body" />').appendTo(this.element);
        this.resizeHandle = $('<div class="phpdebugbar-resize-handle" />').appendTo(this.body);
        this.firstPanelName = null;
        this.activePanelName = null;

        // allow resizing by dragging handle
        this.body.drag('start', function(e, dd) {
            dd.height = $(this).height();
        }).drag(function(e, dd) {
            var h = Math.max(100, dd.height - dd.deltaY);
            $(this).css('height', h);
            localStorage.setItem('phpdebugbar-height', h);
        }, {handle: '.phpdebugbar-resize-handle'});
        
        // close button
        this.closeButton = $('<a class="phpdebugbar-close-btn" href="javascript:"><i class="icon-remove"></i></a>').appendTo(this.header);
        this.closeButton.click(function() {
            self.hidePanels();
        });

        // select box for data sets
        this.datasetSelectBox = $('<select class="phpdebugbar-datasets-switcher" />').appendTo(this.header);
        this.datasetSelectBox.change(function() {
            self.dataChangeHandler(self.datasets[this.value]);
        });
    };

    /**
     * Custom initialiaze function for subsclasses
     *
     * @this {DebugBar}
     */
    DebugBar.prototype.init = function() {};

    /**
     * Restores the state of the DebugBar using localStorage
     * This is not called by default in the constructor and
     * needs to be called by subclasses in their init() method
     *
     * @this {DebugBar}
     */
    DebugBar.prototype.restoreState = function() {
        // bar height
        var height = localStorage.getItem('phpdebugbar-height');
        if (height) {
            this.body.css('height', height);
        } else {
            localStorage.setItem('phpdebugbar-height', this.body.height());
        }

        // bar visibility
        var visible = localStorage.getItem('phpdebugbar-visible');
        if (visible && visible == '1') {
            this.showPanel(localStorage.getItem('phpdebugbar-panel'));
        }
    };

    /**
     * Creates and adds a new tab
     *
     * @this {DebugBar}
     * @param {String} name Internal name
     * @param {Object} widget A widget object with an element property
     * @param {String} title The text in the tab, if not specified, name will be used
     * @return {Tab}
     */
    DebugBar.prototype.createTab = function(name, widget, title) {
        var tab = new Tab(title || (name.replace(/[_\-]/g, ' ').charAt(0).toUpperCase() + name.slice(1)), widget);
        return this.addTab(name, tab);
    };

    /**
     * Adds a new tab
     *
     * @this {DebugBar}
     * @param {String} name Internal name
     * @param {Tab} tab Tab object
     * @return {Tab}
     */
    DebugBar.prototype.addTab = function(name, tab) {
        if (this.isControl(name)) {
            throw new Exception(name + ' already exists');
        }

        var self = this;
        tab.tab.appendTo(this.header).click(function() { self.showPanel(name); });
        tab.panel.appendTo(this.body);

        this.controls[name] = tab;
        if (this.firstPanelName == null) {
            this.firstPanelName = name;
        }
        return tab;
    };

    /**
     * Returns a Tab object
     * 
     * @this {DebugBar}
     * @param {String} name
     * @return {Tab}
     */
    DebugBar.prototype.getTab = function(name) {
        if (this.isTab(name)) {
            return this.controls[name];
        }
    };

    /**
     * Creates and adds an indicator
     *
     * @this {DebugBar}
     * @param {String} name Internal name
     * @param {String} icon
     * @param {String} tooltip
     * @param {String} position "right" or "left", default is "right"
     * @return {Indicator}
     */
    DebugBar.prototype.createIndicator = function(name, icon, tooltip, position) {
        var indicator = new Indicator(icon, tooltip, position);
        return this.addIndicator(name, indicator);
    };

    /**
     * Adds an indicator
     * 
     * @this {DebugBar}
     * @param {String} name Internal name
     * @param {Indicator} indicator Indicator object
     * @return {Indicator}
     */
    DebugBar.prototype.addIndicator = function(name, indicator) {
        if (this.isControl(name)) {
            throw new Exception(name + ' already exists');
        }

        if (indicator.position == 'right') {
            indicator.element.appendTo(this.header);
        } else {
            indicator.element.insertBefore(this.header.children().first())
        }

        this.controls[name] = indicator;
        return indicator;
    };

    /**
     * Returns an Indicator object
     * 
     * @this {DebugBar}
     * @param {String} name
     * @return {Indicator}
     */
    DebugBar.prototype.getIndicator = function(name) {
        if (this.isIndicator(name)) {
            return this.controls[name];
        }
    };

    /**
     * Adds a control
     * 
     * @param {String} name
     * @param {Object} control
     * @return {Object}
     */
    DebugBar.prototype.addControl = function(name, control) {
        if (control instanceof Tab) {
            this.addTab(name, control);
        } else if (control instanceof Indicator) {
            this.addIndicator(name, control);
        } else {
            throw new Exception("Unknown type of control");
        }
        return control;
    };

    /**
     * Checks if there's a control under the specified name
     * 
     * @this {DebugBar}
     * @param {String} name
     * @return {Boolean}
     */
    DebugBar.prototype.isControl = function(name) {
        return typeof(this.controls[name]) != 'undefined';
    };

    /**
     * Checks if a tab with the specified name exists
     * 
     * @this {DebugBar}
     * @param {String} name
     * @return {Boolean}
     */
    DebugBar.prototype.isTab = function(name) {
        return this.isControl(name) && this.controls[name] instanceof Tab;
    };

    /**
     * Checks if an indicator with the specified name exists
     * 
     * @this {DebugBar}
     * @param {String} name
     * @return {Boolean}
     */
    DebugBar.prototype.isIndicator = function(name) {
        return this.isControl(name) && this.controls[name] instanceof Indicator;
    };

    /**
     * Removes all tabs and indicators from the debug bar and hides it
     * 
     * @this {DebugBar}
     */
    DebugBar.prototype.reset = function() {
        this.hidePanels();
        this.body.find('.phpdebugbar-panel').remove();
        this.header.find('.phpdebugbar-tab, .phpdebugbar-indicator').remove();
        this.controls = {};
    };

    /**
     * Open the debug bar and display a specified panel
     * 
     * @this {DebugBar}
     * @param {String} name If not specified, display the first panel
     */
    DebugBar.prototype.showPanel = function(name) {
        this.resizeHandle.show();
        this.body.show();
        this.closeButton.show();

        if (!name) {
            if (this.activePanelName) {
                name = this.activePanelName;
            } else {
                name = this.firstPanelName;
            }
        }

        this.header.find('.phpdebugbar-tab.active').removeClass('active');
        this.body.find('.phpdebugbar-panel.active').removeClass('active');

        if (this.isTab(name)) {
            this.controls[name].tab.addClass('active');
            this.controls[name].panel.addClass('active').show();
            this.activePanelName = name;
        }
        localStorage.setItem('phpdebugbar-visible', '1');
        localStorage.setItem('phpdebugbar-panel', name);
    };

    /**
     * Shows the first panel
     * 
     * @this {DebugBar}
     */
    DebugBar.prototype.showFirstPanel = function() {
        this.showPanel(this.firstPanelName);
    };

    /**
     * Hide panels and "close" the debug bar
     *
     * @this {DebugBar}
     */
    DebugBar.prototype.hidePanels = function() {
        this.header.find('.phpdebugbar-tab.active').removeClass('active');
        this.body.hide();
        this.closeButton.hide();
        this.resizeHandle.hide();
        localStorage.setItem('phpdebugbar-visible', '0');
    };

    /**
     * Sets the data map used by dataChangeHandler to populate
     * indicators and widgets
     *
     * A data map is an object where properties are control names.
     * The value of each property should be an array where the first
     * item is the name of a property from the data object (nested properties
     * can be specified) and the second item the default value.
     *
     * Example:
     *     {"memory": ["memory.peak_usage_str", "0B"]}
     * 
     * @this {DebugBar}
     * @param {Object} map
     */
    DebugBar.prototype.setDataMap = function(map) {
        this.dataMap = map;
    };

    /**
     * Same as setDataMap() but appends to the existing map
     * rather than replacing it
     *
     * @this {DebugBar}
     * @param {Object} map
     */
    DebugBar.prototype.addDataMap = function(map) {
        $.extend(this.dataMap, map);
    };

    /**
     * Resets datasets and add one set of data
     *
     * For this method to be usefull, you need to specify
     * a dataMap using setDataMap()
     * 
     * @this {DebugBar}
     * @param {Object} data
     * @return {String} Dataset's id
     */
    DebugBar.prototype.setData = function(data) {
        this.datasets = {};
        return this.addDataSet(data);
    };

    /**
     * Adds a dataset
     *
     * If more than one dataset are added, the dataset selector
     * will be displayed.
     * 
     * For this method to be usefull, you need to specify
     * a dataMap using setDataMap()
     * 
     * @this {DebugBar}
     * @param {Object} data
     * @param {String} id The name of this set, optional
     * @return {String} Dataset's id
     */
    DebugBar.prototype.addDataSet = function(data, id) {
        id = id || ("Request #" + (Object.keys(this.datasets).length + 1));
        this.datasets[id] = data;

        this.datasetSelectBox.append($('<option value="' + id + '">' + id + '</option>'));
        if (Object.keys(this.datasets).length > 1) {
            this.datasetSelectBox.show();
        }

        this.showDataSet(id);
        return id;
    };

    /**
     * Returns the data from a dataset
     * 
     * @this {DebugBar}
     * @param {String} id
     * @return {Object}
     */
    DebugBar.prototype.getDataSet = function(id) {
        return this.datasets[id];
    };

    /**
     * Switch the currently displayed dataset
     * 
     * @this {DebugBar}
     * @param {String} id
     */
    DebugBar.prototype.showDataSet = function(id) {
        this.dataChangeHandler(this.datasets[id]);
        this.datasetSelectBox.val(id);
    };

    /**
     * Called when the current dataset is modified.
     * 
     * @this {DebugBar}
     * @param {Object} data
     */
    DebugBar.prototype.dataChangeHandler = function(data) {
        var self = this;
        $.each(this.dataMap, function(key, def) {
            var d = getDictValue(data, def[0], def[1]);
            if (self.isIndicator(key)) {
                self.getIndicator(key).setText(d);
            } else {
                self.getTab(key).widget.setData(d);
            }
        });
    };

    DebugBar.Tab = Tab;
    DebugBar.Indicator = Indicator;

    return DebugBar;

})(jQuery);
