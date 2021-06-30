(function($) {

    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the displaying sql queries
     *
     * Options:
     *  - data
     */
    var SQLQueriesWidget = PhpDebugBar.Widgets.SQLQueriesWidget = PhpDebugBar.Widget.extend({

        className: csscls('sqlqueries'),

        onFilterClick: function(el) {
            $(el).toggleClass(csscls('excluded'));

            var excludedLabels = [];
            this.$toolbar.find(csscls('.filter') + csscls('.excluded')).each(function() {
                excludedLabels.push(this.rel);
            });

            this.$list.$el.find("li[connection=" + $(el).attr("rel") + "]").toggle();

            this.set('exclude', excludedLabels);
        },
        onCopyToClipboard: function (el) {
            var code = $(el).parent('li').find('code').get(0);
            var copy = function () {
                try {
                    document.execCommand('copy');
                    alert('Query copied to the clipboard');
                } catch (err) {
                    console.log('Oops, unable to copy');
                }
            };
            var select = function (node) {
                if (document.selection) {
                    var range = document.body.createTextRange();
                    range.moveToElementText(node);
                    range.select();
                } else if (window.getSelection) {
                    var range = document.createRange();
                    range.selectNodeContents(node);
                    window.getSelection().removeAllRanges();
                    window.getSelection().addRange(range);
                }
                copy();
                window.getSelection().removeAllRanges();
            };
            select(code);
        },
        getListSelectOrder: function () {
            var list = $('.phpdebugbar-widgets-list-item');
            var defaultOptions = [
                {val: 0, text: 'ORDER'},
                {val: 3, text: 'SQL'}
            ];
            if (list.length === 0) {
                return [];
            }
            var firstItem = list.eq(0);
            var hasDuration = firstItem.find('.phpdebugbar-widgets-duration').length;
            var hasMemory = firstItem.find('.phpdebugbar-widgets-duration').length;
            if (hasDuration === 1) {
                defaultOptions.push({val: 1, text: 'DURATION'});
            }
            if (hasMemory === 1) {
                defaultOptions.push({val: 2, text: 'MEMORY USE'});
            }
            return defaultOptions;
        },
        order: function (el) {
            var element = $(el);
            var option = parseInt(element.val(), 10);
            var listTarget = '.phpdebugbar-widgets-memory';
            var orderBy = function (a, b) {
                var attrCompare = 'data-memory';
                if (option === 1) {
                    attrCompare = 'data-duration';
                    listTarget = '.phpdebugbar-widgets-duration';
                }
                if (option === 3) {
                    listTarget = '.phpdebugbar-widgets-sql';
                    return ($(b).find(listTarget).text()) < ($(a).find(listTarget).text()) ? 1 : -1;
                }
                if (option === 0) {
                    attrCompare = 'data-order';
                    listTarget = '.phpdebugbar-widgets-sql';
                    return (parseInt($(b).find(listTarget).attr(attrCompare), 10)) < (parseInt($(a).find(listTarget).attr(attrCompare), 10)) ? 1 : -1;
                }
                return (parseFloat($(b).find(listTarget).attr(attrCompare))) > (parseFloat($(a).find(listTarget).attr(attrCompare))) ? 1 : -1;
            };
            $('.phpdebugbar-widgets-sqlqueries ul li').sort(orderBy).appendTo('.phpdebugbar-widgets-sqlqueries ul');
        },
        render: function() {
            this.$status = $('<div />').addClass(csscls('status')).appendTo(this.$el);

            this.$toolbar = $('<div></div>').addClass(csscls('toolbar')).appendTo(this.$el);

            var filters = [], self = this, count = 0;

            this.$list = new PhpDebugBar.Widgets.ListWidget({ itemRenderer: function(li, stmt) {
                $('<code />').attr('data-order', ++count).addClass(csscls('sql')).html(PhpDebugBar.Widgets.highlight(stmt.sql, 'sql')).appendTo(li);
                if (stmt.duration_str) {
                    $('<span title="Duration" />').attr('data-duration', stmt.duration).addClass(csscls('duration')).text(stmt.duration_str).appendTo(li);
                }
                if (stmt.memory_str) {
                    $('<span title="Memory usage" />').attr('data-memory', stmt.memory).addClass(csscls('memory')).text(stmt.memory_str).appendTo(li);
                }
                if (typeof(stmt.row_count) != 'undefined') {
                    $('<span title="Row count" />').addClass(csscls('row-count')).text(stmt.row_count).appendTo(li);
                }
                if (typeof(stmt.stmt_id) != 'undefined' && stmt.stmt_id) {
                    $('<span title="Prepared statement ID" />').addClass(csscls('stmt-id')).text(stmt.stmt_id).appendTo(li);
                }
                if (stmt.connection) {
                    $('<span title="Connection" />').addClass(csscls('database')).text(stmt.connection).appendTo(li);
                    li.attr("connection",stmt.connection);
                    if ( $.inArray(stmt.connection, filters) == -1 ) {
                        filters.push(stmt.connection);
                        $('<a />')
                            .addClass(csscls('filter'))
                            .text(stmt.connection)
                            .attr('rel', stmt.connection)
                            .on('click', function() { self.onFilterClick(this); })
                            .appendTo(self.$toolbar);
                        if (filters.length>1) {
                            self.$toolbar.show();
                            self.$list.$el.css("margin-bottom","20px");
                        }
                    }
                }
                if (typeof(stmt.is_success) != 'undefined' && !stmt.is_success) {
                    li.addClass(csscls('error'));
                    li.append($('<span />').addClass(csscls('error')).text("[" + stmt.error_code + "] " + stmt.error_message));
                }
                $('<span title="Copy to clipboard" />')
                    .addClass(csscls('copy-clipboard'))
                    .css('cursor', 'pointer')
                    .on('click', function (event) {
                        self.onCopyToClipboard(this);
                        event.stopPropagation();
                    })
                    .appendTo(li);
                if (stmt.params && !$.isEmptyObject(stmt.params)) {
                    var table = $('<table><tr><th colspan="2">Params</th></tr></table>').addClass(csscls('params')).appendTo(li);
                    for (var key in stmt.params) {
                        if (typeof stmt.params[key] !== 'function') {
                            table.append('<tr><td class="' + csscls('name') + '">' + key + '</td><td class="' + csscls('value') +
                                '">' + stmt.params[key] + '</td></tr>');
                        }
                    }
                    li.css('cursor', 'pointer').click(function() {
                        if (table.is(':visible')) {
                            table.hide();
                        } else {
                            table.show();
                        }
                    });
                }
            }});
            this.$list.$el.appendTo(this.$el);

            this.bindAttr('data', function(data) {
                // the PDO collector maybe is empty
                if (data.length <= 0) {
                    return false;
                }
                this.$list.set('data', data.statements);
                this.$status.empty();

                // Search for duplicate statements.
                for (var sql = {}, unique = 0, duplicate = 0, i = 0; i < data.statements.length; i++) {
                    var stmt = data.statements[i].sql;
                    if (data.statements[i].params && !$.isEmptyObject(data.statements[i].params)) {
                        stmt += ' {' + $.param(data.statements[i].params, false) + '}';
                    }
                    sql[stmt] = sql[stmt] || { keys: [] };
                    sql[stmt].keys.push(i);
                }
                // Add classes to all duplicate SQL statements.
                for (var stmt in sql) {
                    if (sql[stmt].keys.length > 1) {
                        duplicate += sql[stmt].keys.length;
                        for (var i = 0; i < sql[stmt].keys.length; i++) {
                            this.$list.$el.find('.' + csscls('list-item')).eq(sql[stmt].keys[i])
                                .addClass(csscls('sql-duplicate'));
                        }
                    } else {
                        unique++;
                    }
                }

                var t = $('<span />').text(data.nb_statements + " statements were executed").appendTo(this.$status);
                var sel = $('<select>');
                var options = self.getListSelectOrder();
                var spanOrder = $('<span/>');
                $(options).each(function () {
                    sel.append($("<option>").attr('value', this.val).text(this.text));
                });
                sel.on('click', function (event) {
                    self.order(this);
                    event.stopPropagation();
                });
                spanOrder.addClass(csscls('order')).append(sel);
                this.$status.append(spanOrder);
                if (data.nb_failed_statements) {
                    t.append(", " + data.nb_failed_statements + " of which failed");
                }
                if (duplicate) {
                    t.append(", " + duplicate + " of which were duplicates");
                    t.append(", " + unique + " unique");
                }
                if (data.accumulated_duration_str) {
                    this.$status.append($('<span title="Accumulated duration" />').addClass(csscls('duration')).text(data.accumulated_duration_str));
                }
                if (data.memory_usage_str) {
                    this.$status.append($('<span title="Memory usage" />').addClass(csscls('memory')).text(data.memory_usage_str));
                }
            });
        }

    });

})(PhpDebugBar.$);
