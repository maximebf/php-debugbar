# PHP Debug Bar

[![Latest Stable Version](https://poser.pugx.org/maximebf/debugbar/v/stable.png)](https://packagist.org/packages/maximebf/debugbar) [![Total Downloads](https://poser.pugx.org/maximebf/debugbar/downloads.svg)](https://packagist.org/packages/maximebf/debugbar) [![License](https://poser.pugx.org/maximebf/debugbar/license.svg)](https://packagist.org/packages/maximebf/debugbar) [![Build Status](https://travis-ci.org/maximebf/php-debugbar.png?branch=master)](https://travis-ci.org/maximebf/php-debugbar)

Displays a debug bar in the browser with information from php.
No more `var_dump()` in your code!

![Screenshot](https://raw.github.com/maximebf/php-debugbar/master/docs/screenshot.png)

**Features:**

 - Generic debug bar
 - Easy to integrate with any project
 - Clean, fast and easy to use interface
 - Handles AJAX request
 - Includes generic data collectors and collectors for well known libraries
 - The client side bar is 100% coded in javascript
 - Easily create your own collectors and their associated view in the bar
 - Save and re-open previous requests
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

Integrations with other frameworks:

  - [Laravel](https://github.com/barryvdh/laravel-debugbar)
  - [Atomik](http://atomikframework.com/docs/error-log-debug.html#debug-bar)
  - [XOOPS](http://xoops.org/modules/news/article.php?storyid=6538)
  - [Zend Framework 2](https://github.com/snapshotpl/ZfSnapPhpDebugBar)

*(drop me a message or submit a PR to add your DebugBar related project here)*

## Installation

The best way to install DebugBar is using [Composer](http://getcomposer.org)
with the following command:

```composer require maximebf/debugbar```

## Quick start

DebugBar is very easy to use and you can add it to any of your projects in no time.
The easiest way is using the `render()` functions

```PHP
<?php

// Require the Composer autoloader, if not already loaded
require 'vendor/autoload.php';

use DebugBar\StandardDebugBar;

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
```

The DebugBar uses DataCollectors to collect data from your PHP code. Some of them are
automated but others are manual. Use the `DebugBar` like an array where keys are the
collector names. In our previous example, we add a message to the `MessagesCollector`:

```PHP
$debugbar["messages"]->addMessage("hello world!");
```

`StandardDebugBar` activates the following collectors:

 - `MemoryCollector` (*memory*)
 - `MessagesCollector` (*messages*)
 - `PhpInfoCollector` (*php*)
 - `RequestDataCollector` (*request*)
 - `TimeDataCollector` (*time*)
 - `ExceptionsCollector` (*exceptions*)

Learn more about DebugBar in the [docs](http://phpdebugbar.com/docs).
