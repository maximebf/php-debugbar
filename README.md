# PHP Debug Bar

Displays a debug bar in the browser with information from php.
No more `var_dump()` in your code!

![Screenshot](https://raw.github.com/maximebf/php-debugbar/master/docs/screenshot.png)

**Features:**

 - Generic debug bar with no other dependencies
 - Easy to integrate with any project
 - Clean, fast and easy to use interface
 - Handles AJAX request
 - Includes generic data collectors and collectors for well known libraries
 - The client side bar is 100% coded in javascript
 - Easily create your own collectors and their associated view in the bar
 - [Very well documented](http://phpdebugbar.com/docs)

Includes collectors for:

  - [PDO](http://php.net/manual/en/book.pdo.php)
  - [CacheCache](http://maximebf.github.io/CacheCache/)
  - [Doctrine](http://doctrine-project.org)
  - [Monolog](https://github.com/Seldaek/monolog)
  - [Propel](http://propelorm.org/)
  - [Slim](http://slimframework.com)
  - [Swift Mailer](http://swiftmailer.org/)
  - [Twig](http://twig.sensiolabs.org/)

Checkout the [demo](https://github.com/maximebf/php-debugbar/tree/master/demo) for
examples and [phpdebugbar.com](http://phpdebugbar.com) for a live example.

## Installation

The easiest way to install DebugBar is using [Composer](https://github.com/composer/composer)
with the following requirement:

    {
        "require": {
            "maximebf/debugbar": ">=1.0.0"
        }
    }

Alternatively, you can [download the archive](https://github.com/maximebf/php-debugbar/zipball/master) 
and add the src/ folder to PHP's include path:

    set_include_path('/path/to/src' . PATH_SEPARATOR . get_include_path());

DebugBar does not provide an autoloader but follows the [PSR-0 convention](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).  
You can use the following snippet to autoload DebugBar classes:

    spl_autoload_register(function($className) {
        if (substr($className, 0, 8) === 'DebugBar') {
            $filename = str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\\')) . '.php';
            require_once $filename;
        }
    });

## Quick start

DebugBar is very easy to use and you can add it to any of your projets in no time.
The easiest way is using the `render()` functions

    <?php
    use DebugBar\StandardDebugBar;
    use DebugBar\JavascriptRenderer;

    $debugbar = new StandardDebugBar();
    $debugbarRenderer = $debugbar->getJavascriptRenderer();

    $debugbar["messages"]->addMessage("hello world!");
    ?>
    <html>
        <head>
            <?php echo $debugbarRenderer->renderHead() ?>
        </head>
        <body>
            ...
            <?php echo $debugbarRenderer->render() ?>
        </body>
    </html>

The DebugBar uses DataCollectors to collect data from your PHP code. Some of them are
automated but others are manual. Use the `DebugBar` like an array where keys are the
collector names. In our previous example, we add a message to the `MessagesCollector`:

    $debugbar["messages"]->addMessage("hello world!");

`StandardDebugBar` activates the following collectors:

 - `MemoryCollector` (*memory*)
 - `MessagesCollector` (*messages*)
 - `PhpInfoCollector` (*php*)
 - `RequestDataCollector` (*request*)
 - `TimeDataCollector` (*time*)
 - `ExceptionsCollector` (*exceptions*)

Learn more about DebugBar in the [docs](http://phpdebugbar.com/docs).
