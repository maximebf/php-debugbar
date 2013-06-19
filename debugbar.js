$(function() {

var phpdebugbar = new PhpDebugBar.DebugBar();

phpdebugbar.createTab("messages", new PhpDebugBar.Widgets.MessagesWidget());
phpdebugbar.createTab("request", new PhpDebugBar.Widgets.VariableListWidget());
phpdebugbar.createIndicator("time", "time", "Request Duration");
phpdebugbar.createTab("timeline", new PhpDebugBar.Widgets.TimelineWidget());
phpdebugbar.createIndicator("memory", "cogs", "Memory Usage");
phpdebugbar.createTab("exceptions", new PhpDebugBar.Widgets.ExceptionsWidget());
phpdebugbar.createTab("database", new PhpDebugBar.Widgets.SQLQueriesWidget());

phpdebugbar.setDataMap({
    "messages": ["messages.messages", []],
    "messages:badge": ["messages.count", null],
    "request": ["request", {}],
    "time": ["time.duration_str", '0ms'],
    "timeline": ["time", {}],
    "memory": ["memory.peak_usage_str", '0B'],
    "exceptions": ["exceptions.exceptions", []],
    "exceptions:badge": ["exceptions.count", null],
    "database": ["pdo", []],
    "database:badge": ["pdo.nb_statements", 0]
});

phpdebugbar.restoreState();

phpdebugbar.addDataSet({
    "php": {
        "version": "5.3.10-1ubuntu3.4"
    },
    "messages": {
        "count": 4,
        "messages": [
            {
                "message": "hello",
                "is_string": true,
                "label": "info",
                "time": 1371613602.4755,
                "memory_usage": 1112416,
                "backtrace": [
                    {
                        "file": "/media/sf_Projects/Code/PHP/php-debugbar/demo/demo.php",
                        "line": 5,
                        "function": "addMessage",
                        "class": "DebugBar\\DataCollector\\MessagesCollector",
                        "object": {},
                        "type": "->",
                        "args": [
                            "hello"
                        ]
                    }
                ]
            },
            {
                "message": "world",
                "is_string": true,
                "label": "warning",
                "time": 1371613602.4767,
                "memory_usage": 1122096,
                "backtrace": [
                    {
                        "file": "/media/sf_Projects/Code/PHP/php-debugbar/demo/demo.php",
                        "line": 15,
                        "function": "addMessage",
                        "class": "DebugBar\\DataCollector\\MessagesCollector",
                        "object": {},
                        "type": "->",
                        "args": [
                            "world",
                            "warning"
                        ]
                    }
                ]
            },
            {
                "message": "Array\n(\n    [toto] => Array\n        (\n            [0] => titi\n            [1] => tata\n        )\n\n)\n",
                "is_string": false,
                "label": "info",
                "time": 1371613602.4767,
                "memory_usage": 1128240,
                "backtrace": [
                    {
                        "file": "/media/sf_Projects/Code/PHP/php-debugbar/demo/demo.php",
                        "line": 16,
                        "function": "addMessage",
                        "class": "DebugBar\\DataCollector\\MessagesCollector",
                        "object": {},
                        "type": "->",
                        "args": [
                            {
                                "toto": [
                                    "titi",
                                    "tata"
                                ]
                            }
                        ]
                    }
                ]
            },
            {
                "message": "oups",
                "is_string": true,
                "label": "error",
                "time": 1371613602.4768,
                "memory_usage": 1132904,
                "backtrace": [
                    {
                        "file": "/media/sf_Projects/Code/PHP/php-debugbar/demo/demo.php",
                        "line": 17,
                        "function": "addMessage",
                        "class": "DebugBar\\DataCollector\\MessagesCollector",
                        "object": {},
                        "type": "->",
                        "args": [
                            "oups",
                            "error"
                        ]
                    }
                ]
            }
        ]
    },
    "request": {
        "$_GET": "Array\n(\n)\n",
        "$_POST": "Array\n(\n)\n",
        "$_COOKIE": "Array\n(\n    [PHPSESSID] => qj57lsa5hrtamv0d28sfgq3sv3\n)\n",
        "$_SERVER": "Array\n(\n    [HTTP_HOST] => 192.168.56.101\n    [HTTP_CONNECTION] => keep-alive\n    [HTTP_ACCEPT] => text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\n    [HTTP_USER_AGENT] => Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.110 Safari/537.36\n    [HTTP_ACCEPT_ENCODING] => gzip,deflate,sdch\n    [HTTP_ACCEPT_LANGUAGE] => en-US,en;q=0.8,fr;q=0.6\n    [HTTP_COOKIE] => PHPSESSID=qj57lsa5hrtamv0d28sfgq3sv3\n    [PATH] => /usr/local/bin:/usr/bin:/bin\n    [SERVER_SIGNATURE] => <address>Apache/2.2.22 (Ubuntu) Server at 192.168.56.101 Port 80</address>\n\n    [SERVER_SOFTWARE] => Apache/2.2.22 (Ubuntu)\n    [SERVER_NAME] => 192.168.56.101\n    [SERVER_ADDR] => 192.168.56.101\n    [SERVER_PORT] => 80\n    [REMOTE_ADDR] => 192.168.56.1\n    [DOCUMENT_ROOT] => /var/www\n    [SERVER_ADMIN] => webmaster@localhost\n    [SCRIPT_FILENAME] => /var/www/php-debugbar/demo/demo.php\n    [REMOTE_PORT] => 36930\n    [GATEWAY_INTERFACE] => CGI/1.1\n    [SERVER_PROTOCOL] => HTTP/1.1\n    [REQUEST_METHOD] => GET\n    [QUERY_STRING] => \n    [REQUEST_URI] => /php-debugbar/demo/demo.php\n    [SCRIPT_NAME] => /php-debugbar/demo/demo.php\n    [PHP_SELF] => /php-debugbar/demo/demo.php\n    [REQUEST_TIME] => 1371613602\n)\n"
    },
    "time": {
        "start":1371614268.3245,
        "end":1371614268.3349,
        "duration":0.010406970977783,
        "duration_str":"10ms",
        "measures": [
            {
                "label": "sleep 500",
                "start": 1371614268.3331,
                "relative_start": 0.0085549354553223,
                "end": 1371614268.3339,
                "relative_end": 1371614268.3339,
                "duration": 0.00081396102905273,
                "duration_str": "1ms"
            },
            {
                "label": "sleep 400",
                "start": 1371614268.3336,
                "relative_start": 0.0090389251708984,
                "end": 1371614268.3343,
                "relative_end": 1371614268.3343,
                "duration": 0.0007469654083252,
                "duration_str": "1ms"
            },
            {
                "label": "render",
                "start": 1371614268.3344,
                "relative_start": 0.0098698139190674,
                "end": 1371614268.3349,
                "relative_end": 0.000010013580322266,
                "duration": 0.00054717063903809,
                "duration_str": "1ms"
            }
        ]
    },
    "memory": {
        "peak_usage": 1310720,
        "peak_usage_str": "1.25MB"
    },
    "exceptions": {
        "count": 0,
        "exceptions": []
    },
    "pdo": {
        "nb_statements": 6,
        "nb_failed_statements": 1,
        "accumulated_duration": 0.0007481575012207,
        "accumulated_duration_str": "1ms",
        "peak_memory_usage": 1572864,
        "peak_memory_usage_str": "1.5MB",
        "statements": [
            {
                "sql": "create table users (name varchar)",
                "row_count": 0,
                "stmt_id": null,
                "prepared_stmt": "create table users (name varchar)",
                "params": [],
                "duration": 0.00041103363037109,
                "duration_str": "0ms",
                "memory": 1310720,
                "memory_str": "1.25MB",
                "is_success": true,
                "error_code": 0,
                "error_message": ""
            },
            {
                "sql": "insert into users (name) values (<foo>)",
                "row_count": 1,
                "stmt_id": "000000006ef9f64b00000000a141d954",
                "prepared_stmt": "insert into users (name) values (?)",
                "params": [
                    "foo"
                ],
                "duration": 0.000044107437133789,
                "duration_str": "0ms",
                "memory": 1572864,
                "memory_str": "1.5MB",
                "is_success": true,
                "error_code": 0,
                "error_message": ""
            },
            {
                "sql": "insert into users (name) values (<bar>)",
                "row_count": 1,
                "stmt_id": "000000006ef9f64b00000000a141d954",
                "prepared_stmt": "insert into users (name) values (?)",
                "params": [
                    "bar"
                ],
                "duration": 0.000030040740966797,
                "duration_str": "0ms",
                "memory": 1572864,
                "memory_str": "1.5MB",
                "is_success": true,
                "error_code": 0,
                "error_message": ""
            },
            {
                "sql": "select * from users",
                "row_count": 0,
                "stmt_id": null,
                "prepared_stmt": "select * from users",
                "params": [],
                "duration": 0.000041007995605469,
                "duration_str": "0ms",
                "memory": 1572864,
                "memory_str": "1.5MB",
                "is_success": true,
                "error_code": 0,
                "error_message": ""
            },
            {
                "sql": "select * from users where name=<foo>",
                "row_count": 0,
                "stmt_id": "000000006ef9f65400000000a141d954",
                "prepared_stmt": "select * from users where name=?",
                "params": [
                    "foo"
                ],
                "duration": 0.000021934509277344,
                "duration_str": "0ms",
                "memory": 1572864,
                "memory_str": "1.5MB",
                "is_success": true,
                "error_code": 0,
                "error_message": ""
            },
            {
                "sql": "delete from unknown_table",
                "row_count": 0,
                "stmt_id": null,
                "prepared_stmt": "delete from unknown_table",
                "params": [],
                "duration": 0.00020003318786621,
                "duration_str": "0ms",
                "memory": 1572864,
                "memory_str": "1.5MB",
                "is_success": false,
                "error_code": 0,
                "error_message": "no such table: unknown_table"
            }
        ]
    },
    "exceptions": {
        "count": 1,
        "exceptions": [
            {
                "type": "Exception",
                "message": "Something failed!",
                "code": 0,
                "file": "/php-debugbar/demo/failed.php",
                "line": 6,
                "surrounding_lines": [
                    "include 'bootstrap.php';\n",
                    "\n",
                    "try {\n",
                    "    throw new Exception('Something failed!');\n",
                    "} catch (Exception $e) {\n",
                    "    $debugbar['exceptions']->addException($e);\n",
                    "}\n"
                ]
            }
        ]
    }
});

});
