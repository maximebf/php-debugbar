<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\Widget;

/**
 * Represents a tab in the client side debugbar
 */
class Tab extends AbstractWidget
{
    protected $className = 'PhpDebugBar.DebugBar.Tab';

    protected $widgetClassName;

    protected $widgetCtorOptions = array();

    public function __construct($title, $widgetClassName, $dataMap, $defaultValue = 'null', $icon = null)
    {
        $this->title = $title;
        $this->widgetClassName = $widgetClassName;
        $this->mapping = $dataMap;
        $this->defaultValue = $defaultValue;
        $this->icon = $icon;
    }

    public function setWidgetClassName($className)
    {
        $this->widgetClassName = $className;
        return $this;
    }

    public function getWidgetClassName()
    {
        return $this->widgetClassName;
    }

    public function setWidgetCtorOptions($options)
    {
        $this->widgetCtorOptions = $options;
        return $this;
    }

    public function getWidgetCtorOptions()
    {
        return $this->widgetCtorOptions;
    }
}