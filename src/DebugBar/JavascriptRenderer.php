<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar;

use DebugBar\DataCollector\WidgetProvider;
use DebugBar\DataCollector\AssetProvider;

/**
 * Renders the debug bar using the client side javascript implementation
 *
 * Generates all the needed initialization code of controls
 */
class JavascriptRenderer
{
    const INITIALIZE_CONSTRUCTOR = 2;

    const INITIALIZE_CONTROLS = 4;

    const REPLACEABLE_TAG = "{--DEBUGBAR_OB_START_REPLACE_ME--}";

    const RELATIVE_PATH = 'path';

    const RELATIVE_URL = 'url';

    protected $debugBar;

    protected $baseUrl;

    protected $basePath;

    protected $cssVendors = array(
        'vendor/font-awesome/css/font-awesome.min.css',
        'vendor/highlightjs/styles/github.css'
    );

    protected $jsVendors = array(
        'vendor/jquery/dist/jquery.min.js',
        'vendor/highlightjs/highlight.pack.js'
    );

    protected $includeVendors = true;

    protected $cssFiles = array('debugbar.css', 'widgets.css', 'openhandler.css');

    protected $jsFiles = array('debugbar.js', 'widgets.js', 'openhandler.js');

    protected $additionalAssets = array();

    protected $javascriptClass = 'PhpDebugBar.DebugBar';

    protected $variableName = 'phpdebugbar';

    protected $enableJqueryNoConflict = true;

    protected $initialization;

    protected $widgets = array();

    protected $ignoredCollectors = array();

    protected $ajaxHandlerClass = 'PhpDebugBar.AjaxHandler';

    protected $ajaxHandlerBindToJquery = true;

    protected $openHandlerClass = 'PhpDebugBar.OpenHandler';

    protected $serverHandlerUrl;

    protected $constructorOptions = array();

    /**
     * @param \DebugBar\DebugBar $debugBar
     * @param string $baseUrl
     * @param string $basePath
     */
    public function __construct(DebugBar $debugBar, $baseUrl = null, $basePath = null)
    {
        $this->debugBar = $debugBar;

        if ($baseUrl === null) {
            //calculate $baseUrl from docroot should the docroot exist in the $_SERVER global else revert to the default
            if( array_key_exists('DOCUMENT_ROOT',$_SERVER) ){
                $baseUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__ .'/Resources');
            } else {
                $baseUrl = '/vendor/maximebf/debugbar/src/DebugBar/Resources';
            }
        }
        $this->baseUrl = $baseUrl;

        if ($basePath === null) {
            $basePath = __DIR__ . DIRECTORY_SEPARATOR . 'Resources';
        }
        $this->basePath = $basePath;

        // bitwise operations cannot be done in class definition :(
        $this->initialization = self::INITIALIZE_CONSTRUCTOR | self::INITIALIZE_CONTROLS;
    }

    /**
     * Sets options from an array
     *
     * Options:
     *  - base_path
     *  - base_url
     *  - include_vendors
     *  - javascript_class
     *  - variable_name
     *  - initialization
     *  - enable_jquery_noconflict
     *  - widgets
     *  - disable_widgets
     *  - ignore_collectors
     *  - ajax_handler_classname
     *  - ajax_handler_bind_to_jquery
     *  - open_handler_classname
     *  - server_handler_url
     *  - ctor_options
     *
     * @param array $options [description]
     */
    public function setOptions(array $options)
    {
        if (array_key_exists('base_path', $options)) {
            $this->setBasePath($options['base_path']);
        }
        if (array_key_exists('base_url', $options)) {
            $this->setBaseUrl($options['base_url']);
        }
        if (array_key_exists('include_vendors', $options)) {
            $this->setIncludeVendors($options['include_vendors']);
        }
        if (array_key_exists('javascript_class', $options)) {
            $this->setJavascriptClass($options['javascript_class']);
        }
        if (array_key_exists('variable_name', $options)) {
            $this->setVariableName($options['variable_name']);
        }
        if (array_key_exists('initialization', $options)) {
            $this->setInitialization($options['initialization']);
        }
        if (array_key_exists('enable_jquery_noconflict', $options)) {
            $this->setEnableJqueryNoConflict($options['enable_jquery_noconflict']);
        }
        if (array_key_exists('widgets', $options)) {
            foreach ($options['widgets'] as $name => $widget) {
                $this->addWidget($name, $widget);
            }
        }
        if (array_key_exists('disable_widgets', $options)) {
            foreach ((array) $options['disable_widgets'] as $name) {
                $this->disableWidget($name);
            }
        }
        if (array_key_exists('ignore_collectors', $options)) {
            foreach ((array) $options['ignore_collectors'] as $name) {
                $this->ignoreCollector($name);
            }
        }
        if (array_key_exists('ajax_handler_classname', $options)) {
            $this->setAjaxHandlerClass($options['ajax_handler_classname']);
        }
        if (array_key_exists('ajax_handler_bind_to_jquery', $options)) {
            $this->setBindAjaxHandlerToJquery($options['ajax_handler_bind_to_jquery']);
        }
        if (array_key_exists('open_handler_classname', $options)) {
            $this->setOpenHandlerClass($options['open_handler_classname']);
        }
        if (array_key_exists('server_handler_url', $options)) {
            $this->setServerHandlerUrl($options['server_handler_url']);
        }
        if (array_key_exists('ctor_options', $options)) {
            $this->setConstructorOptions($options['ctor_options']);
        }
    }

    /**
     * Sets the path which assets are relative to
     *
     * @param string $path
     */
    public function setBasePath($path)
    {
        $this->basePath = $path;
        return $this;
    }

    /**
     * Returns the path which assets are relative to
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Sets the base URL from which assets will be served
     *
     * @param string $url
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * Returns the base URL from which assets will be served
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Whether to include vendor assets
     *
     * You can only include js or css vendors using
     * setIncludeVendors('css') or setIncludeVendors('js')
     *
     * @param boolean $enabled
     */
    public function setIncludeVendors($enabled = true)
    {
        if (is_string($enabled)) {
            $enabled = array($enabled);
        }
        $this->includeVendors = $enabled;

        if (!$enabled || (is_array($enabled) && !in_array('js', $enabled))) {
            // no need to call jQuery.noConflict() if we do not include our own version
            $this->enableJqueryNoConflict = false;
        }

        return $this;
    }

    /**
     * Checks if vendors assets are included
     *
     * @return boolean
     */
    public function areVendorsIncluded()
    {
        return $this->includeVendors !== false;
    }

    /**
     * Sets the javascript class name
     *
     * @param string $className
     */
    public function setJavascriptClass($className)
    {
        $this->javascriptClass = $className;
        return $this;
    }

    /**
     * Returns the javascript class name
     *
     * @return string
     */
    public function getJavascriptClass()
    {
        return $this->javascriptClass;
    }

    /**
     * Sets the variable name of the class instance
     *
     * @param string $name
     */
    public function setVariableName($name)
    {
        $this->variableName = $name;
        return $this;
    }

    /**
     * Returns the variable name of the class instance
     *
     * @return string
     */
    public function getVariableName()
    {
        return $this->variableName;
    }

    /**
     * Sets what should be initialized
     *
     *  - INITIALIZE_CONSTRUCTOR: only initializes the instance
     *  - INITIALIZE_CONTROLS: initializes the controls and data mapping
     *  - INITIALIZE_CONSTRUCTOR | INITIALIZE_CONTROLS: initialize everything (default)
     *
     * @param integer $init
     */
    public function setInitialization($init)
    {
        $this->initialization = $init;
        return $this;
    }

    /**
     * Returns what should be initialized
     *
     * @return integer
     */
    public function getInitialization()
    {
        return $this->initialization;
    }

    /**
     * Sets whether to call jQuery.noConflict()
     *
     * @param boolean $enabled
     */
    public function setEnableJqueryNoConflict($enabled = true)
    {
        $this->enableJqueryNoConflict = $enabled;
        return $this;
    }

    /**
     * Checks if jQuery.noConflict() will be called
     *
     * @return boolean
     */
    public function isJqueryNoConflictEnabled()
    {
        return $this->enableJqueryNoConflict;
    }

    /**
     * Ignores widgets provided by a collector
     *
     * @param string $name
     */
    public function ignoreCollector($name)
    {
        $this->ignoredCollectors[] = $name;
        return $this;
    }

    /**
     * Returns the list of ignored collectors
     *
     * @return array
     */
    public function getIgnoredCollectors()
    {
        return $this->ignoredCollectors;
    }

    /**
     * Sets the class name of the ajax handler
     *
     * Set to false to disable
     *
     * @param string $className
     */
    public function setAjaxHandlerClass($className)
    {
        $this->ajaxHandlerClass = $className;
        return $this;
    }

    /**
     * Returns the class name of the ajax handler
     *
     * @return string
     */
    public function getAjaxHandlerClass()
    {
        return $this->ajaxHandlerClass;
    }

    /**
     * Sets whether to call bindToJquery() on the ajax handler
     *
     * @param boolean $bind
     */
    public function setBindAjaxHandlerToJquery($bind = true)
    {
        $this->ajaxHandlerBindToJquery = $bind;
        return $this;
    }

    /**
     * Checks whether bindToJquery() will be called on the ajax handler
     *
     * @return boolean
     */
    public function isAjaxHandlerBoundToJquery()
    {
        return $this->ajaxHandlerBindToJquery;
    }

    /**
     * Sets the class name of the js open handler
     *
     * @param string $className
     */
    public function setOpenHandlerClass($className)
    {
        $this->openHandlerClass = $className;
        return $this;
    }

    /**
     * Returns the class name of the js open handler
     *
     * @return string
     */
    public function getOpenHandlerClass()
    {
        return $this->openHandlerClass;
    }

    /**
     * Sets the url of the server handler
     *
     * @param string $url
     */
    public function setServerHandlerUrl($url)
    {
        $this->serverHandlerUrl = $url;
        return $this;
    }

    /**
     * Returns the url for the server handler
     *
     * @return string
     */
    public function getServerHandlerUrl()
    {
        return $this->serverHandlerUrl;
    }

    /**
     * Sets debugbar's constructor options
     *
     * @param array $options
     * @return $this
     */
    public function setConstructorOptions(array $options)
    {
        $this->constructorOptions = $options;
        return $this;
    }

    /**
     * Returns debugbar's constructor options
     *
     * @return array
     */
    public function getConstructorOptions()
    {
        return $this->constructorOptions;
    }

    /**
     * Adds a widget
     *
     * @param string $name
     * @param AbstractWidget|DataMap $widget
     * @return $this
     */
    public function addWidget($name, $widget)
    {
        $this->widgets[$name] = $widget;
        return $this;
    }

    /**
     * Disables a widget
     *
     * @param string $name
     * @return $this
     */
    public function disableWidget($name)
    {
        $this->widgets[$name] = null;
        return $this;
    }

    /**
     * Returns the list of widgets
     *
     * This does not include widgets provided by collectors
     *
     * @return array
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * Add assets to render in the head
     *
     * @param array $cssFiles An array of filenames
     * @param array $jsFiles  An array of filenames
     * @param string $basePath Base path of those files
     * @param string $baseUrl  Base url of those files
     * @return $this
     */
    public function addAssets($cssFiles, $jsFiles, $basePath = null, $baseUrl = null)
    {
        $this->additionalAssets[] = array(
            'base_path' => $basePath,
            'base_url' => $baseUrl,
            'css' => (array) $cssFiles,
            'js' => (array) $jsFiles
        );
        return $this;
    }

    /**
     * Returns the list of asset files
     *
     * @param string $type Only return css or js files
     * @param string $relativeTo The type of path to which filenames must be relative (path, url or null)
     * @return array
     */
    public function getAssets($type = null, $relativeTo = self::RELATIVE_PATH)
    {
        $cssFiles = $this->cssFiles;
        $jsFiles = $this->jsFiles;

        if ($this->includeVendors !== false) {
            if ($this->includeVendors === true || in_array('css', $this->includeVendors)) {
                $cssFiles = array_merge($this->cssVendors, $cssFiles);
            }
            if ($this->includeVendors === true || in_array('js', $this->includeVendors)) {
                $jsFiles = array_merge($this->jsVendors, $jsFiles);
            }
        }

        if ($relativeTo) {
            $root = $this->getRelativeRoot($relativeTo, $this->basePath, $this->baseUrl);
            $cssFiles = $this->makeUriRelativeTo($cssFiles, $root);
            $jsFiles = $this->makeUriRelativeTo($jsFiles, $root);
        }

        $additionalAssets = $this->additionalAssets;
        // finds assets provided by collectors
        foreach ($this->debugBar->getCollectors() as $collector) {
            if (($collector instanceof AssetProvider) && !in_array($collector->getName(), $this->ignoredCollectors)) {
                $additionalAssets[] = $collector->getAssets();
            }
        }

        foreach ($additionalAssets as $assets) {
            $basePath = isset($assets['base_path']) ? $assets['base_path'] : null;
            $baseUrl = isset($assets['base_url']) ? $assets['base_url'] : null;
            $root = $this->getRelativeRoot($relativeTo,
                $this->makeUriRelativeTo($basePath, $this->basePath),
                $this->makeUriRelativeTo($baseUrl, $this->baseUrl));
            $cssFiles = array_merge($cssFiles, $this->makeUriRelativeTo((array) $assets['css'], $root));
            $jsFiles = array_merge($jsFiles, $this->makeUriRelativeTo((array) $assets['js'], $root));
        }

        return $this->filterAssetArray(array($cssFiles, $jsFiles), $type);
    }

    /**
     * Returns the correct base according to the type
     *
     * @param string $relativeTo
     * @param string $basePath
     * @param string $baseUrl
     * @return string
     */
    protected function getRelativeRoot($relativeTo, $basePath, $baseUrl)
    {
        if ($relativeTo === self::RELATIVE_PATH) {
            return $basePath;
        }
        if ($relativeTo === self::RELATIVE_URL) {
            return $baseUrl;
        }
        return null;
    }

    /**
     * Makes a URI relative to another
     *
     * @param string|array $uri
     * @param string $root
     * @return string
     */
    protected function makeUriRelativeTo($uri, $root)
    {
        if (!$root) {
            return $uri;
        }

        if (is_array($uri)) {
            $uris = array();
            foreach ($uri as $u) {
                $uris[] = $this->makeUriRelativeTo($u, $root);
            }
            return $uris;
        }

        if (substr($uri, 0, 1) === '/' || preg_match('/^([a-z]+:\/\/|[a-zA-Z]:\/)/', $uri)) {
            return $uri;
        }
        return rtrim($root, '/') . "/$uri";
    }

    /**
     * Filters a tuple of (css, js) assets according to $type
     *
     * @param array $array
     * @param string $type 'css', 'js' or null for both
     * @return array
     */
    protected function filterAssetArray($array, $type = null)
    {
        $type = strtolower($type);
        if ($type === 'css') {
            return $array[0];
        }
        if ($type === 'js') {
            return $array[1];
        }
        return $array;
    }

    /**
     * Returns a tuple where the both items are Assetic AssetCollection,
     * the first one being css files and the second js files
     *
     * @param string $type Only return css or js collection
     * @return array or \Assetic\Asset\AssetCollection
     */
    public function getAsseticCollection($type = null)
    {
        list($cssFiles, $jsFiles) = $this->getAssets();
        return $this->filterAssetArray(array(
            $this->createAsseticCollection($cssFiles),
            $this->createAsseticCollection($jsFiles)
        ), $type);
    }

    /**
     * Create an Assetic AssetCollection with the given files.
     * Filenames will be converted to absolute path using
     * the base path.
     *
     * @param array $files
     * @return \Assetic\Asset\AssetCollection
     */
    protected function createAsseticCollection($files)
    {
        $assets = array();
        foreach ($files as $file) {
            $assets[] = new \Assetic\Asset\FileAsset($file);
        }
        return new \Assetic\Asset\AssetCollection($assets);
    }

    /**
     * Write all CSS assets to standard output or in a file
     *
     * @param string $targetFilename
     */
    public function dumpCssAssets($targetFilename = null)
    {
        $this->dumpAssets($this->getAssets('css'), $targetFilename);
    }

    /**
     * Write all JS assets to standard output or in a file
     *
     * @param string $targetFilename
     */
    public function dumpJsAssets($targetFilename = null)
    {
        $this->dumpAssets($this->getAssets('js'), $targetFilename);
    }

    /**
     * Write assets to standard output or in a file
     *
     * @param array $files
     * @param string $targetFilename
     */
    protected function dumpAssets($files, $targetFilename = null)
    {
        $content = '';
        foreach ($files as $file) {
            $content .= file_get_contents($file) . "\n";
        }
        if ($targetFilename !== null) {
            file_put_contents($targetFilename, $content);
        } else {
            echo $content;
        }
    }

    /**
     * Renders the html to include needed assets
     *
     * Only useful if Assetic is not used
     *
     * @return string
     */
    public function renderHead()
    {
        list($cssFiles, $jsFiles) = $this->getAssets(null, self::RELATIVE_URL);
        $html = '';

        foreach ($cssFiles as $file) {
            $html .= sprintf('<link rel="stylesheet" type="text/css" href="%s">' . "\n", $file);
        }

        foreach ($jsFiles as $file) {
            $html .= sprintf('<script type="text/javascript" src="%s"></script>' . "\n", $file);
        }

        return $html;
    }

    /**
     * Register shutdown to display the debug bar
     *
     * @param boolean $here Set position of HTML. True if is to current position or false for end file
     * @param boolean $initialize Whether to render the de bug bar initialization code
     * @return string Return "{--DEBUGBAR_OB_START_REPLACE_ME--}" or return an empty string if $here == false
     */
    public function renderOnShutdown($here = true, $initialize = true, $renderStackedData = true, $head = false)
    {
        register_shutdown_function(array($this, "replaceTagInBuffer"), $here, $initialize, $renderStackedData, $head);

        if (ob_get_level() === 0) {
            ob_start();
        }

        return ($here) ? self::REPLACEABLE_TAG : "";
    }

    /**
     * Same as renderOnShutdown() with $head = true
     *
     * @param boolean $here
     * @param boolean $initialize
     * @param boolean $renderStackedData
     * @return string
     */
    public function renderOnShutdownWithHead($here = true, $initialize = true, $renderStackedData = true)
    {
        return $this->renderOnShutdown($here, $initialize, $renderStackedData, true);
    }

    /**
     * Is callback function for register_shutdown_function(...)
     *
     * @param boolean $here Set position of HTML. True if is to current position or false for end file
     * @param boolean $initialize Whether to render the de bug bar initialization code
     */
    public function replaceTagInBuffer($here = true, $initialize = true, $renderStackedData = true, $head = false)
    {
        $render = ($head ? $this->renderHead() : "")
                . $this->render($initialize, $renderStackedData);

        $current = ($here && ob_get_level() > 0) ? ob_get_clean() : self::REPLACEABLE_TAG;

        echo str_replace(self::REPLACEABLE_TAG, $render, $current, $count);

        if ($count === 0) {
            echo $render;
        }
    }

    /**
     * Returns the code needed to display the debug bar
     *
     * AJAX request should not render the initialization code.
     *
     * @param boolean $initialize Whether to render the de bug bar initialization code
     * @return string
     */
    public function render($initialize = true, $renderStackedData = true)
    {
        $js = '';

        if ($initialize) {
            $js = $this->getJsInitializationCode();
        }

        if ($renderStackedData && $this->debugBar->hasStackedData()) {
            foreach ($this->debugBar->getStackedData() as $id => $data) {
                $js .= $this->getAddDatasetCode($id, $data, '(stacked)');
            }
        }

        $suffix = !$initialize ? '(ajax)' : null;
        $js .= $this->getAddDatasetCode($this->debugBar->getCurrentRequestId(), $this->debugBar->getData(), $suffix);

        return "<script type=\"text/javascript\">\n$js\n</script>\n";
    }

    /**
     * Returns the js code needed to initialize the debug bar
     *
     * @return string
     */
    protected function getJsInitializationCode()
    {
        $js = '';

        if ($this->enableJqueryNoConflict) {
            $js .= "jQuery.noConflict(true);\n";
        }

        if (($this->initialization & self::INITIALIZE_CONSTRUCTOR) === self::INITIALIZE_CONSTRUCTOR) {
            $ctorOptions = $this->constructorOptions;
            if ($this->serverHandlerUrl) {
                $ctorOptions['serverHandlerUrl'] = $this->serverHandlerUrl;
            }
            $js .= sprintf("var %s = new %s(%s);\n", $this->variableName,
                $this->javascriptClass, json_encode($ctorOptions, JSON_FORCE_OBJECT));
        }

        if (($this->initialization & self::INITIALIZE_CONTROLS) === self::INITIALIZE_CONTROLS) {
            $js .= $this->getJsControlsDefinitionCode($this->variableName);
        }

        if ($this->ajaxHandlerClass) {
            $js .= sprintf("%s.ajaxHandler = new %s(%s);\n", $this->variableName, $this->ajaxHandlerClass, $this->variableName);
            if ($this->ajaxHandlerBindToJquery) {
                $js .= sprintf("if (jQuery) %s.ajaxHandler.bindToJquery(jQuery);\n", $this->variableName);
            }
        }

        if ($this->debugBar->isDataPersisted() && $this->serverHandlerUrl !== null) {
            $js .= sprintf("%s.setOpenHandler(new %s());\n", $this->variableName, $this->openHandlerClass);
        }

        return $js;
    }

    /**
     * Returns the js code needed to initialized the controls and data mapping of the debug bar
     *
     * Controls can be defined by collectors themselves or using {@see addWidget()}
     *
     * @param string $varname Debug bar's variable name
     * @return string
     */
    protected function getJsControlsDefinitionCode($varname)
    {
        $js = '';
        $dataMap = array();

        // finds widgets provided by collectors
        $widgets = array();
        foreach ($this->debugBar->getCollectors() as $collector) {
            if (($collector instanceof WidgetProvider) && !in_array($collector->getName(), $this->ignoredCollectors)) {
                if ($w = $collector->getWidgets()) {
                    $widgets = array_merge($widgets, $w);
                }
            }
        }
        $widgets = array_merge($widgets, $this->widgets);


        foreach (array_filter($widgets) as $name => $widget) {
            if ($widget instanceof Widget\Tab) {
                $opts = $widget->getConstructorOptions();
                if (empty($opts['title'])) {
                    $opts['title'] = ucfirst(str_replace('_', ' ', $name));
                }
                $widgetClassName = $widget->getWidgetClassName();
                $js .= sprintf("%s.addTab(\"%s\", new %s({%s%s}));\n",
                    $varname,
                    $name,
                    $widget->getClassName(),
                    substr(json_encode($opts, JSON_FORCE_OBJECT), 1, -1),
                    $widgetClassName ? sprintf('%s"widget":new %s(%s)',
                        count($opts) ? ',' : '', $widgetClassName, json_encode($widget->getWidgetCtorOptions(), JSON_FORCE_OBJECT)) : ''
                );
            } else if ($widget instanceof Widget\Indicator) {
                $js .= sprintf("%s.addIndicator(\"%s\", new %s(%s), \"%s\");\n",
                    $varname,
                    $name,
                    $widget->getClassName(),
                    json_encode($widget->getConstructorOptions(), JSON_FORCE_OBJECT),
                    $widget->getPosition()
                );
            }

            if ($widget instanceof Widget\DataMap) {
                $dataMap[$name] = array($widget->getMapping(), $widget->getDefaultValue());
            }
        }

        // creates the data mapping object
        $mapJson = array();
        foreach ($dataMap as $name => $values) {
            $mapJson[] = sprintf('"%s": ["%s", %s]', $name, $values[0], $values[1]);
        }
        $js .= sprintf("%s.setDataMap({\n%s\n});\n", $varname, implode(",\n", $mapJson));

        // activate state restoration
        $js .= sprintf("%s.restoreState();\n", $varname);

        return $js;
    }

    /**
     * Returns the js code needed to add a dataset
     *
     * @param string $requestId
     * @param array $data
     * @return string
     */
    protected function getAddDatasetCode($requestId, $data, $suffix = null)
    {
        $js = sprintf("%s.addDataSet(%s, \"%s\"%s);\n",
            $this->variableName,
            json_encode($data),
            $requestId,
            $suffix ? ", " . json_encode($suffix) : ''
        );
        return $js;
    }
}
