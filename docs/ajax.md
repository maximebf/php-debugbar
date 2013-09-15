# AJAX

As mentioned in the previous chapter, if you are performing AJAX requests
which return HTML content, you can use `JavascriptRenderer::render(false)`.

In the case you are sending back non-HTML data (eg: JSON), the DebugBar can 
send data to the client using HTTP headers using the `sendDataInHeaders()` method
(no need to use the `JavascriptRenderer`):

    $debugbar = new DebugBar();
    // ...
    $debugbar->sendDataInHeaders();

On the client side, an instance of `PhpDebugBar.AjaxHandler` will
parse the headers and add the dataset to the debugbar.

The AjaxHandler automatically binds to jQuery's *ajaxComplete* event
so if you are using jQuery, you have nothing to configure.

If you're not using jQuery, you can call `AjaxHandler.handle(xhr)`.
If you are using the `JavascriptRenderer` initialization, the instance 
of `AjaxHandler` is stored in the `ajaxHandler` property of the `DebugBar` object.

    debugbar.ajaxHandler.handle(xhr);