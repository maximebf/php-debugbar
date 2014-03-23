(function($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    var CacheWidget = PhpDebugBar.Widgets.CacheWidget = PhpDebugBar.Widget.extend({

        className: csscls('cache'),

        render: function() {
            this.$list = new  PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, record) {
                li.html(record.message);
            }});
            this.$list.$el.appendTo(this.$el);

            this.bindAttr('data', function(data) {
                this.$list.set('data', data);
            });

            this.$toolbar = $('<div />').addClass(csscls('toolbar'));
            this.$toolbar.append($('<a href="#">Clear all</a>').on('click', function() {
                this.preventDefault();
                PhpDebugBar.DebugBar.instance.callServer('cache', 'clear', function() {
                    alert('Done!')
                });
            }));
            this.$toolbar.append($('<a href="#">Clear key</a>').on('click', function() {
                this.preventDefault();
                var key = prompt("Key name:");
                if (!key) {
                    return;
                }
                PhpDebugBar.DebugBar.instance.callServer('cache', 'clearKey', {key:key}, function() {
                    alert('Done!')
                });
            }));
        }

    });

})(PhpDebugBar.$);