# Storage

DebugBar supports storing collected data for later analysis.
You'll need to set a storage handler using `setStorage()` on your `DebugBar` instance.

    $debugbar->setStorage(new DebugBar\Storage\FileStorage('/path/to/dir'));

Each time `DebugBar::collect()` is called, the data will be persisted.

## Available storage

Only file storage is provided at the moment. It will collected data as json files
under the specified directory (which as to be writable).

    $storage = new DebugBar\Storage\FileStorage($directory);

## Creating your own storage

You can easily create your own storage handler by implementing the
`DebugBar\Storage\StorageInterface`.

## Request ID generator

For each request, the debug bar will generate a unique id under which to store the 
collected data. This is perform using a `DebugBar\RequestIdGeneratorInterface` object.

If none are defined, the debug bar will automatically use `DebugBar\RequestIdGenerator`
which uses the `$_SERVER` array to generate the id.