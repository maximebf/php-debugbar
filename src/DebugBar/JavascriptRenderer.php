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

use DebugBar\DataCollector\Renderable;

/**
 * Renders the debug bar using the client side javascript implementation
 *
 * Generates all the needed initialization code of controls
 */
class JavascriptRenderer
{
    const INITIALIZE_CONSTRUCTOR = 2;

    const INITIALIZE_CONTROLS = 4;

    protected $debugBar;

    protected $baseUrl;

    protected $basePath;

    protected $cssVendors = array('vendor/font-awesome/css/font-awesome.css');

    protected $jsVendors = array('vendor/jquery-1.8.3.min.js', 'vendor/jquery.event.drag-2.2.js');

    protected $includeVendors = true;

    protected $cssFiles = array('debugbar.css');

    protected $jsFiles = array('debugbar.js', 'widgets.js');

    protected $javascriptClass = 'PhpDebugBar.DebugBar';

    protected $variableName = 'phpdebugbar';

    protected $initialization;

    protected $controls = array();

    /**
     * @param DebugBar $debugBar
     * @param string $baseUrl
     * @param string $basePath
     */
    public function __construct(DebugBar $debugBar, $baseUrl = '/debugbar', $basePath = null)
    {
        $this->debugBar = $debugBar;
        $this->baseUrl = $baseUrl;

        if ($basePath === null) {
            $basePath = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array('..', '..', '..', 'web'));
        }
        $this->basePath = $basePath;

        // bitwise operations cannot be done in class definition :(
        $this->initialization = self::INITIALIZE_CONSTRUCTOR | self::INITIALIZE_CONTROLS;
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
     * @param boolean $enabled
     */
    public function setIncludeVendors($enabled = true)
    {
        $this->includeVendors = $enabled;
        return $this;
    }

    /**
     * Checks if vendors assets are included
     * 
     * @return boolean
     */
    public function areVendorsIncluded()
    {
        return $this->includeVendors;
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
     * Adds a control to initialize
     *
     * Possible options:
     *  - icon: icon name
     *  - tooltip: string
     *  - widget: widget class name
     *  - map: a property name from the data to map the control to
     *  - default: a js string, default value of the data map
     *
     * "icon" or "widget" are at least needed
     * 
     * @param string $name
     * @param array $options
     */
    public function addControl($name, $options)
    {
        if (!isset($options['icon']) || !isset($options['widget'])) {
            throw new DebugBarException("Missing 'icon' or 'widget' option for control '$name'");
        }
        $this->controls[$name] = $options;
        return $this;
    }

    /**
     * Returns the list of asset files
     * 
     * @return array
     */
    protected function getAssetFiles()
    {
        $cssFiles = $this->cssFiles;
        $jsFiles = $this->jsFiles;

        if ($this->includeVendors) {
            $cssFiles = array_merge($this->cssVendors, $cssFiles);
            $jsFiles = array_merge($this->jsVendors, $jsFiles);
        }

        return array($cssFiles, $jsFiles);
    }

    /**
     * Returns a tuple where the both items are Assetic AssetCollection,
     * the first one being css files and the second js files
     *
     * @return array or \Assetic\Asset\AssetCollection
     */
    public function getAsseticCollection()
    {
        list($cssFiles, $jsFiles) = $this->getAssetFiles();
        return array(
            $this->createAsseticCollection($cssFiles),
            $this->createAsseticCollection($jsFiles)
        );
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
            $assets[] = new \Assetic\Asset\FileAsset($this->makeUriRelativeTo($file, $this->basePath));
        }
        return new \Assetic\Asset\AssetCollection($assets);
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
        list($cssFiles, $jsFiles) = $this->getAssetFiles();
        $html = '';

        foreach ($cssFiles as $file) {
            $html .= sprintf('<link rel="stylesheet" type="text/css" href="%s">' . "\n", 
                $this->makeUriRelativeTo($file, $this->baseUrl));
        }

        foreach ($jsFiles as $file) {
            $html .= sprintf('<script type"text/javascript" src="%s"></script>' . "\n", 
                $this->makeUriRelativeTo($file, $this->baseUrl));
        }

        return $html;
    }

    /**
     * Makes a URI relative to another
     * 
     * @param string $uri
     * @param string $root
     * @return string
     */
    protected function makeUriRelativeTo($uri, $root)
    {
        if (substr($uri, 0, 1) === '/' || preg_match('/^([a-z]+:\/\/|[a-zA-Z]:\/)/', $uri)) {
            return $uri;
        }
        return rtrim($root, '/') . "/$uri";
    }

    /**
     * Returns the code needed to display the debug bar
     *
     * AJAX request should not render the initialization code.
     * 
     * @param boolean $initialize Whether to render the de bug bar initialization code
     * @return string
     */
    public function render($initialize = true)
    {
        $js = '';

        if ($initialize) {
            $js = $this->getJsInitializationCode();
        }
        
        $js .= sprintf("%s.addDataSet(%s);\n", $this->variableName, json_encode($this->debugBar->getData()));
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

        if (($this->initialization & self::INITIALIZE_CONSTRUCTOR) === self::INITIALIZE_CONSTRUCTOR) {
            $js = sprintf("var %s = new %s();\n", $this->variableName, $this->javascriptClass);
        }

        if (($this->initialization & self::INITIALIZE_CONTROLS) === self::INITIALIZE_CONTROLS) {
            $js .= $this->getJsControlsDefinitionCode($this->variableName);
        }

        return $js;
    }

    /**
     * Returns the js code needed to initialized the controls and data mapping of the debug bar
     *
     * Controls can be defined by collectors themselves or using {@see addControl()}
     * 
     * @param string $varname Debug bar's variable name
     * @return string
     */
    protected function getJsControlsDefinitionCode($varname)
    {
        $js = '';
        $dataMap = array();
        $controls = $this->controls;

        // finds controls provided by collectors
        foreach ($this->debugBar->getCollectors() as $collector) {
            if ($collector instanceof Renderable) {
                $controls = array_merge($controls, $collector->getWidgets());
            }
        }

        foreach ($controls as $name => $options) {
            if (isset($options['widget'])) {
                $js .= sprintf("%s.createTab(\"%s\", new %s());\n", 
                    $varname,
                    $name, 
                    $options['widget']
                );
            } else {
                $js .= sprintf("%s.createIndicator(\"%s\", \"%s\", \"%s\");\n", 
                    $varname,
                    $name,
                    isset($options['icon']) ? $options['icon'] : 'null', 
                    isset($options['tooltip']) ? $options['tooltip'] : 'null'
                );
            }

            if (isset($options['map']) && isset($options['default'])) {
                $dataMap[$name] = array($options['map'], $options['default']);
            }
        }

        // creates the data mapping object
        $mapJson = array();
        foreach ($dataMap as $name => $values) {
            $mapJson[] = sprintf('"%s": ["%s", %s]', $name, $values[0], $values[1]);
        }
        $js .= sprintf("%s.setDataMap({\n%s\n});\n", $varname, implode(",\n", $mapJson));

        // activate state restauration
        $js .= sprintf("%s.restoreState();\n", $varname);

        return $js;
    }
}
