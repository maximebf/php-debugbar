if (typeof(PhpDebugBar) == 'undefined') {
    // namespace
    var PhpDebugBar = {};
}

/**
 * @namespace
 */
PhpDebugBar.Widgets = (function($) {

    var widgets = {};

    /**
     * Replaces spaces with &nbsp; and line breaks with <br>
     * 
     * @param {String} text
     * @return {String}
     */
    var htmlize = function(text) {
        return text.replace(/\n/g, '<br>').replace(/\s/g, "&nbsp;")
    };

    widgets.htmlize = htmlize;

    /**
     * Returns a string representation of value, using JSON.stringify
     * if it's an object.
     * 
     * @param {Object} value
     * @param {Boolean} prettify Uses htmlize() if true
     * @return {String}
     */
    var renderValue = function(value, prettify) {
        if (typeof(value) !== 'string') {
            if (prettify) {
                return htmlize(JSON.stringify(value, undefined, 2));
            }
            return JSON.stringify(value);
        }
        return value;
    };

    widgets.renderValue = renderValue;


    // ------------------------------------------------------------------
    // Generic widgets
    // ------------------------------------------------------------------

    /**
     * Displays array element in a <ul> list
     *
     * @this {ListWidget}
     * @constructor
     * @param {Array} data
     * @param {Function} itemRenderer Optional
     */
    var ListWidget = function(data, itemRenderer) {
        this.element = $('<ul class="phpdebugbar-widgets-list" />');
        if (itemRenderer) {
            this.itemRenderer = itemRenderer;
        }
        if (data) {
            this.setData(data);
        }
    };

    /**
     * Sets the data and updates the list
     *
     * @this {ListWidget}
     * @param {Array} data
     */
    ListWidget.prototype.setData = function(data) {
        this.element.empty();
        for (var i = 0; i < data.length; i++) {
            var li = $('<li class="list-item" />').appendTo(this.element);
            this.itemRenderer(li, data[i]);
        }
    };

    /**
     * Renders the content of a <li> element
     *
     * @this {ListWidget}
     * @param {jQuery} li The <li> element as a jQuery Object
     * @param {Object} value An item from the data array
     */
    ListWidget.prototype.itemRenderer = function(li, value) {
        li.html(renderValue(value));
    };

    widgets.ListWidget = ListWidget;

    // ------------------------------------------------------------------

    /**
     * Displays object property/value paris in a <dl> list
     *
     * @this {KVListWidget}
     * @constructor
     * @param {Object} data
     * @param {Function} itemRenderer Optional
     */
    var KVListWidget = function(data, itemRenderer) {
        this.element = $('<dl class="phpdebugbar-widgets-kvlist" />');
        if (itemRenderer) {
            this.itemRenderer = itemRenderer;
        }
        if (data) {
            this.setData(data);
        }
    };

    /**
     * Sets the data and updates the list
     *
     * @this {KVListWidget}
     * @param {Object} data
     */
    KVListWidget.prototype.setData = function(data) {
        var self = this;
        this.element.empty();
        $.each(data, function(key, value) {
            var dt = $('<dt class="key" />').appendTo(self.element);
            var dd = $('<dd class="value" />').appendTo(self.element);
            self.itemRenderer(dt, dd, key, value);
        });
    };

    /**
     * Renders the content of the <dt> and <dd> elements
     *
     * @this {KVListWidget}
     * @param {jQuery} dt The <dt> element as a jQuery Object
     * @param {jQuery} dd The <dd> element as a jQuery Object
     * @param {String} key Property name
     * @param {Object} value Property value
     */
    KVListWidget.prototype.itemRenderer = function(dt, dd, key, value) {
        dt.text(key);
        dd.html(htmlize(value));
    };

    widgets.KVListWidget = KVListWidget;

    // ------------------------------------------------------------------
    
    /**
     * An extension of KVListWidget where the data represents a list
     * of variables
     * 
     * @this {VariableListWidget}
     * @constructor
     * @param {Object} data
     */
    var VariableListWidget = function(data) {
        KVListWidget.apply(this, [data]);
        this.element.addClass('phpdebugbar-widgets-varlist');
    };

    VariableListWidget.prototype = new KVListWidget();
    VariableListWidget.constructor = VariableListWidget;

    VariableListWidget.prototype.itemRenderer = function(dt, dd, key, value) {
        dt.text(key);

        var v = value;
        if (v.length > 100) {
            v = v.substr(0, 100) + "...";
        }
        dd.text(v).click(function() {
            if (dd.hasClass('pretty')) {
                dd.text(v).removeClass('pretty');
            } else {
                dd.html(htmlize(value)).addClass('pretty');
            }
        });
    };

    widgets.VariableListWidget = VariableListWidget;

    // ------------------------------------------------------------------
    
    /**
     * Iframe widget
     *
     * @this {IFrameWidget}
     * @constructor
     * @param {String} url
     */
    var IFrameWidget = function(url) {
        this.element = $('<iframe src="" class="phpdebugbar-widgets-iframe" seamless="seamless" border="0" width="100%" height="100%" />');
        if (url) {
            this.setData(url);
        }
    };

    /**
     * Sets the iframe url
     *
     * @this {IFrameWidget}
     * @param {String} url
     */
    IFrameWidget.prototype.setUrl = function(url) {
        this.element.attr('src', url);
    };

    // for compatibility with data mapping
    IFrameWidget.prototype.setData = function(url) {
        this.setUrl(url);
    };

    widgets.IFrameWidget = IFrameWidget;


    // ------------------------------------------------------------------
    // Collector specific widgets
    // ------------------------------------------------------------------

    /**
     * Widget for the MessagesCollector
     *
     * Uses ListWidget under the hood
     *
     * @this {MessagesWidget}
     * @constructor
     * @param {Array} data
     */
    var MessagesWidget = function(data) {
        this.element = $('<div class="phpdebugbar-widgets-messages" />');

        this.list = new ListWidget(null, function(li, value) {
            var m = value.message;
            if (m.length > 100) {
                m = m.substr(0, 100) + "...";
            }

            var val = $('<span class="value" />').addClass(value.label).text(m).appendTo(li);
            if (!value.is_string) {
                li.css('cursor', 'pointer').click(function() {
                    if (val.hasClass('pretty')) {
                        val.text(m).removeClass('pretty');
                    } else {
                        val.html(htmlize(value.message)).addClass('pretty');
                    }
                });
            }

            //$('<a href="javascript:" class="backtrace"><i class="icon-code-fork"></i></a>').appendTo(li);
            $('<span class="label" />').text(value.label).appendTo(li);
        });
        this.element.append(this.list.element);

        this.toolbar = $('<div class="toolbar"><i class="icon-search"></i></div>').appendTo(this.element);
        var self = this;

        this.searchField = $('<input type="text" />').appendTo(this.toolbar);
        this.searchField.change(function() {
            self.filterData();
        });

        if (data) {
            this.setData(data);
        }
    };

    MessagesWidget.prototype.setData = function(data) {
        this.data = data;
        this.list.setData(data);
        this.toolbar.find('.filter').remove();

        var filters = [], self = this;
        for (var i = 0; i < data.length; i++) {
            if ($.inArray(data[i].label, filters) > -1) {
                continue;
            }
            filters.push(data[i].label);
            $('<a class="filter" href="javascript:" />')
                .text(data[i].label)
                .attr('rel', data[i].label)
                .click(function() { self.toggleFilter($(this).attr('rel')); })
                .appendTo(this.toolbar);
        }
    };

    MessagesWidget.prototype.filterData = function() {
        var filters = this.getEnabledFilters(), 
            searchStr = this.getSearchString(),
            data = [];

        for (var i = 0; i < this.data.length; i++) {
            if ($.inArray(this.data[i].label, filters) > -1 && (!searchStr || this.data[i].message.indexOf(searchStr) > -1)) {
                data.push(this.data[i]);
            }
        }

        this.list.setData(data);
    };

    MessagesWidget.prototype.getSearchString = function(keywords) {
        return this.toolbar.find('input').val();
    };

    MessagesWidget.prototype.toggleFilter = function(filter) {
        this.toolbar.find('.filter[rel="' + filter + '"]').toggleClass('disabled');
        this.filterData();
    };

    MessagesWidget.prototype.getEnabledFilters = function() {
        var filters = [];
        this.toolbar.find('.filter:not(.disabled)').each(function() {
            filters.push(this.rel);
        });
        return filters;
    };

    MessagesWidget.prototype.clear = function() {
        this.setData(this.data);
    };

    widgets.MessagesWidget = MessagesWidget;

    // ------------------------------------------------------------------

    /**
     * Widget for the TimeDataCollector
     *
     * @this {TimelineWidget}
     * @constructor
     * @param {Object} data
     */
    var TimelineWidget = function(data) {
        this.element = $('<ul class="phpdebugbar-widgets-timeline" />');
        if (data) {
            this.setData(data);
        }
    };

    TimelineWidget.prototype.setData = function(data) {
        this.element.empty();
        if (data.measures) {
            for (var i = 0; i < data.measures.length; i++) {
                var li = $('<li class="measure" />');
                li.append($('<span class="label" />').text(data.measures[i].label + " (" + data.measures[i].duration_str + ")"));
                li.append($('<span class="value" />').css({
                    left: Math.round(data.measures[i].relative_start * 100 / data.duration) + "%",
                    width: Math.round(data.measures[i].duration * 100 / data.duration) + "%"
                }));
                this.element.append(li);
            }
        }
    };

    widgets.TimelineWidget = TimelineWidget;

    // ------------------------------------------------------------------

    return widgets;

})(jQuery);
