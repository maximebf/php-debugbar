<?php

namespace DebugBar\Renderer;

use DebugBar\DebugBar;

class JavascriptRenderer
{
    protected $baseUrl = '/';

    protected $cssVendors = array('vendor/font-awesome/css/font-awesome.css');

    protected $jsVendors = array('vendor/jquery-1.8.3.min.js', 'vendor/jquery.event.drag-2.2.js');

    protected $cssFiles = array('debugbar.css');

    protected $jsFiles = array('debugbar.js', 'widgets.js');

    protected $includeVendors = true;

    protected $includeFiles = true;

    protected $toolbarFile = 'standard-debugbar.js';

    protected $toolbarClass = 'StandardPhpDebugBar';

    protected $toolbarVariableName = 'phpdebugbar';

    /**
     * @param \DebugBar\DebugBar $debugBar
     * @param string $baseUrl
     * 
     * @Inject(debugBar="debuBar", baseUrl="$[configuration][webDirectory]")
     */
    public function __construct(DebugBar $debugBar, $baseUrl = '/')
    {
        $this->debugBar = $debugBar;
        $this->setBaseUrl($baseUrl);
    }

    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    public function setIncludeVendors($enabled = true)
    {
        $this->includeVendors = $enabled;
        return $this;
    }

    public function areVendorsIncluded()
    {
        return $this->includeVendors;
    }

    public function setIncludeFiles($enabled = true)
    {
        $this->includeFiles = $enabled;
        return $this;
    }

    public function areFilesIncluded()
    {
        return $this->includeFiles;
    }

    public function setToolbarFile($file)
    {
        $this->toolbarFile = $file;
        return $this;
    }

    public function getToolbarFile()
    {
        return $this->toolbarFile;
    }

    public function setToolbarClass($className)
    {
        $this->toolbarClass = $className;
        return $this;
    }

    public function getToolbarClass()
    {
        return $this->toolbarClass;
    }

    public function setToolbarVariableName($name)
    {
        $this->toolbarVariableName = $name;
    }

    public function getToolbarVariableName()
    {
        return $this->toolbarVariableName;
    }

    public function renderIncludes()
    {
        $cssFiles = array();
        $jsFiles = array();

        if ($this->includeVendors) {
            $cssFiles = array_merge($cssFiles, $this->cssVendors);
            $jsFiles = array_merge($jsFiles, $this->jsVendors);
        }

        if ($this->includeFiles) {
            $cssFiles = array_merge($cssFiles, $this->cssFiles);
            $jsFiles = array_merge($jsFiles, $this->jsFiles);
        }

        $jsFiles[] = $this->toolbarFile;

        $html = '';
        foreach ($cssFiles as $file) {
            $html .= sprintf('<link rel="stylesheet" type="text/css" href="%s">' . "\n",
                $this->makeUrlRelativeTo($file, $this->baseUrl));
        }
        foreach ($jsFiles as $file) {
            $html .= sprintf('<script type"text/javascript" src="%s"></script>' . "\n", 
                $this->makeUrlRelativeTo($file, $this->baseUrl));
        }
        return $html;
    }

    public function renderToolbar()
    {
        return sprintf('<script type="text/javascript">var %s = new %s(%s);</script>' . "\n",
            $this->toolbarVariableName, $this->toolbarClass, json_encode($this->debugBar->getData()));
    }

    public function renderAjaxToolbar()
    {
        return sprintf('<script type="text/javascript">%s.addDataStack(%s);</script>' . "\n",
            $this->toolbarVariableName, json_encode($this->debugBar->getData()));
    }

    public function renderAll()
    {
        return $this->renderIncludes() . $this->renderToolbar();
    }

    protected function makeUrlRelativeTo($url, $root)
    {
        if (substr($url, 0, 1) === '/' || preg_match('/^[a-z]+:\/\//', $url)) {
            return $url;
        }
        return rtrim($root, '/') . "/$url";
    }
}
