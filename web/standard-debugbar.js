
var StandardPhpDebugBar = (function() {

    var DebugBar = function(data) {
        PhpDebugBar.DebugBar.apply(this);
        this.setData(data);
    };

    DebugBar.prototype = new PhpDebugBar.DebugBar();

    DebugBar.prototype.init = function() {
        this.createIndicator('memory', 'cogs', 'Memory Usage');
        this.createIndicator('time', 'time', 'Request duration');
        
        this.createTab('messages', new PhpDebugBar.Widgets.MessagesWidget());
        this.createTab('request', new PhpDebugBar.Widgets.VariableListWidget());
        this.createTab('timeline', new PhpDebugBar.Widgets.TimelineWidget());

        this.dataMap = {
            "memory": ["memory.peak_usage_str", "0B"],
            "time": ["time.duration_str", "0ms"],
            "messages": ["messages", []],
            "request": ["request", {}],
            "timeline": ["time", {}]
        };
    };

    return DebugBar;

})();
