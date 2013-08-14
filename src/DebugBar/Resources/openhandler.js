if (typeof(PhpDebugBar) == 'undefined') {
    // namespace
    var PhpDebugBar = {};
}

(function($) {

    PhpDebugBar.OpenHandler = PhpDebugBar.Widget.extend({

        className: 'phpdebugbar-openhandler',

        render: function() {
            var self = this;
            
            this.$el.appendTo('body').hide();
            this.$ul = $('<ul />').appendTo(this.$el);

            this.$overlay = $('<div class="phpdebugbar-openhandler-overlay" />').hide().appendTo('body');
            this.$overlay.on('click', function() {
                self.$overlay.hide();
                self.$el.hide();
            });
        },

        refresh: function() {
            var self = this;
            this.find({}, 20, 0, function(data) {
                self.$ul.empty();
                $.each(data, function(i, meta) {
                   var a = $('<a href="javascript:" />')
                        .text(meta['datetime'] + ' ' + meta['uri'])
                        .on('click', function(e) {
                            self.$el.hide();
                            self.load(meta['id'], function(data) {
                                self.callback(meta['id'], data);
                            });
                            e.preventDefault();
                        });

                    $('<li />').append(a).appendTo(self.$ul);
                });
            });
        },

        show: function(callback) {
            this.callback = callback;
            this.$el.show();
            this.$overlay.show();
            this.refresh();
        },

        find: function(filters, max, offset, callback)
        {
            var data = $.extend({max: max || 20, offset: offset || 0}, filters);
            $.getJSON(this.get('url'), data, callback);
        },

        load: function(id, callback)
        {
            $.getJSON(this.get('url'), {op: "get", id: id}, callback);
        }

    });

})(jQuery);