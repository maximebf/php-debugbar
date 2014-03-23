# Server handlers

Some widgets one the client side debugbar need to execute commands on the server
side. The DebugBar provides the infrastructure to do so but you will need to
create an endpoint to receive commands.

## The front controller

Commands are handled using a `DebugBar\ServerHandler\FrontController` instance.
You can instanciate one using `DebugBar\DebugBar::createServerHandlerFrontController()`.

    $fc = $debugbar->createServerHandlerFrontController();
    $fc->handle();

Calling `handle()` will use data from the `$_REQUEST` array and echo the output.
The function also supports input from other source if you provide an array as
first argument. It can also return the data instead of echoing (use false as
second argument) and not send the content-type header (use false as third argument).

Once you have setup the fornt controller, tell the `JavascriptRenderer` its url.

    $renderer->setServerHandlerUrl('server.php');

When the FrontController is instanciated, all collectors which implement
`DebugBar\ServerHandler\ServerHandlerInterface` or `DebugBar\ServerHandler\ServerHandlerFactoryInterface`
will be registered. You can register more server handlers using `FrontController::registerHandler()`.

## Creating server handlers

Handlers can be any class implementing the `DebugBar\ServerHandler\ServerHandlerInterface`.
Two methods are required: `getName()` and `getCommandNames()`. The first one must return
a string with this handler's name and the second ones an array of string listing allowed
methods to be called.

A command is simply a method which will receive two arguments, a `$request` array containing
the parameters and the `DebugBar` instance.

    class MyServerHandler implements DebugBar\ServerHandler\ServerHandlerInterface
    {
        public function getName()
        {
            return 'my';
        }

        public function getCommandNames()
        {
            return array('ping');
        }

        public function ping($request, $debugbar)
        {
            return 'pong';
        }
    }

When a command method is called, its return will be sent back as JSON to the client.

## Open handler

The open handler allows you to re-open previously executed requests saved using
the storage mechanism.

The OpenHandler is automatically registered when a FrontController is instanciated
and a storage is defined on the debugbar.

This adds a button in the top right corner of the debug bar which allows you
to browse and open previous sets of collected data.
