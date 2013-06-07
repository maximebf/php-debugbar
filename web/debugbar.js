if (typeof(PhpDebugBar) == 'undefined') {
    var PhpDebugBar = {};
}

if (typeof(localStorage) == 'undefined') {
    var localStorage = {
        setItem: function(key, value) {},
        getItem: function(key) { return null; }
    };
}

PhpDebugBar.DebugBar = (function($) {

    function getDictValue(dict, key, def) {
        var d = dict, parts = key.split('.');
        for (var i = 0; i < parts.length; i++) {
            if (!d[parts[i]]) {
                return def;
            }
            d = d[parts[i]];
        }
        return d;
    }

    var DebugBar = function() {
        this.controls = {};
        this.dataMap = {};
        this.initUI();
        this.init();
        this.restoreState();
    };

    DebugBar.prototype.initUI = function() {
        this.element = $('<div class="phpdebugbar" />').appendTo('body');
        this.header = $('<div class="phpdebugbar-header" />').appendTo(this.element);
        this.body = $('<div class="phpdebugbar-body" />').appendTo(this.element);
        this.resizeHandle = $('<div class="phpdebugbar-resize-handle" />').appendTo(this.body);

        this.body.drag('start', function(e, dd) {
            dd.height = $(this).height();
        }).drag(function(e, dd) {
            var h = Math.max(100, dd.height - dd.deltaY);
            $(this).css('height', h);
            localStorage.setItem('phpdebugbar-height', h);
        }, {handle: '.phpdebugbar-resize-handle'});
        
        this.closeButton = $('<a class="phpdebugbar-close-btn" href="javascript:"><i class="icon-remove"></i></a>').appendTo(this.header);
        var self = this;
        this.closeButton.click(function() {
            self.hidePanels();
        });

        this.stackSelectBox = $('<select class="phpdebugbar-data-stacks" />').appendTo(this.header);
        this.stackSelectBox.change(function() {
            self.dataChangeHandler(self.stacks[this.value]);
        });
    };

    DebugBar.prototype.init = function() {};

    DebugBar.prototype.restoreState = function() {
        var height = localStorage.getItem('phpdebugbar-height');
        if (height) {
            this.body.css('height', height);
        } else {
            localStorage.setItem('phpdebugbar-height', this.body.height());
        }

        var visible = localStorage.getItem('phpdebugbar-visible');
        if (visible && visible == '1') {
            this.showPanel(localStorage.getItem('phpdebugbar-panel'));
        }
    };

    DebugBar.prototype.createTab = function(name, widget, title) {
        if (this.isControl(name)) {
            throw new Exception(name + ' already exists');
        }

        var tab = $('<a href="javascript:" class="phpdebugbar-tab" />').text(title || (name.charAt(0).toUpperCase() + name.slice(1))),
            panel = $('<div class="phpdebugbar-panel" data-id="' + name + '" />'),
            self = this;

        tab.appendTo(this.header).click(function() { self.showPanel(name); });
        panel.appendTo(this.body).append(widget.element);

        this.controls[name] = {type: "tab", tab: tab, panel: panel, widget: widget};
        return widget;
    };

    DebugBar.prototype.setTabTitle = function(name, title) {
        if (this.isTab(name)) {
            this.controls[name].tab.text(title);
        }
    };

    DebugBar.prototype.getTabWidget = function(name) {
        if (this.isTab(name)) {
            return this.controls[name].widget;
        }
    };

    DebugBar.prototype.createIndicator = function(name, icon, tooltip, position) {
        if (this.isControl(name)) {
            throw new Exception(name + ' already exists');
        }
        if (!position) {
            position = 'right'
        }

        var indicator = $('<span class="phpdebugbar-indicator" />').css('float', position),
            text = $('<span class="text" />').appendTo(indicator);

        if (icon) {
            $('<i class="icon-' + icon + '" />').insertBefore(text);
        }
        if (tooltip) {
            indicator.append($('<span class="tooltip" />').text(tooltip));
        }

        if (position == 'right') {
            indicator.appendTo(this.header);
        } else {
            indicator.insertBefore(this.header.children().first())
        }

        this.controls[name] = {type: "indicator", indicator: indicator};
        return text;
    };

    DebugBar.prototype.setIndicatorText = function(name, text) {
        if (this.isIndicator(name)) {
            this.controls[name].indicator.find('.text').text(text);
        }
    };

    DebugBar.prototype.isControl = function(name) {
        return typeof(this.controls[name]) != 'undefined';
    };

    DebugBar.prototype.isTab = function(name) {
        return typeof(this.controls[name]) != 'undefined' && this.controls[name].type === 'tab';
    };

    DebugBar.prototype.isIndicator = function(name) {
        return this.isControl(name) && this.controls[name].type === 'indicator';
    };

    DebugBar.prototype.reset = function() {
        this.hidePanels();
        this.body.find('.phpdebugbar-panel').remove();
        this.header.find('.phpdebugbar-tab, .phpdebugbar-indicator').remove();
        this.controls = {};
    };

    DebugBar.prototype.showPanel = function(name) {
        this.resizeHandle.show();
        this.body.show();
        this.closeButton.show();

        if (!name) {
            activePanel = this.body.find('.phpdebugbar-panel.active');
            if (activePanel.length > 0) {
                name = activePanel.data('id');
            } else {
                name = this.body.find('.phpdebugbar-panel').first().data('id');
            }
        }

        this.header.find('.phpdebugbar-tab.active').removeClass('active');
        this.body.find('.phpdebugbar-panel.active').removeClass('active');

        if (this.isTab(name)) {
            this.controls[name].tab.addClass('active');
            this.controls[name].panel.addClass('active').show();
        }
        localStorage.setItem('phpdebugbar-visible', '1');
        localStorage.setItem('phpdebugbar-panel', name);
    };

    DebugBar.prototype.showFirstPanel = function() {
        this.showPanel(this.body.find('.phpdebugbar-panel').first().data('id'));
    };

    DebugBar.prototype.hidePanels = function() {
        this.header.find('.phpdebugbar-tab.active').removeClass('active');
        this.body.hide();
        this.closeButton.hide();
        this.resizeHandle.hide();
        localStorage.setItem('phpdebugbar-visible', '0');
    };

    DebugBar.prototype.setData = function(data) {
        this.stacks = {};
        this.addDataStack(data);
    };

    DebugBar.prototype.addDataStack = function(data, id) {
        id = id || ("Request #" + (Object.keys(this.stacks).length + 1));
        this.stacks[id] = data;

        this.stackSelectBox.append($('<option value="' + id + '">' + id + '</option>'));
        if (Object.keys(this.stacks).length > 1) {
            this.stackSelectBox.show();
        }

        this.switchDataStack(id);
    };

    DebugBar.prototype.switchDataStack = function(id) {
        this.dataChangeHandler(this.stacks[id]);
        this.stackSelectBox.val(id);
    };

    DebugBar.prototype.dataChangeHandler = function(data) {
        var self = this;
        $.each(this.dataMap, function(key, def) {
            var d = getDictValue(data, def[0], def[1]);
            if (self.isIndicator(key)) {
                self.setIndicatorText(key, d);
            } else {
                self.getTabWidget(key).setData(d);
            }
        });
    };

    return DebugBar;

})(jQuery);
