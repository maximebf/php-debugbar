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
 * Base class for Tab and Indicator
 */
abstract class AbstractWidget extends DataMap
{
    protected $className;

    protected $title;

    protected $icon;

    protected $options = array();

    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    public function getClassName()
    {
        return $this->className;
    }


    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Returns an array with all options for the constructor
     *
     * @return array
     */
    public function getConstructorOptions()
    {
        return array_merge($this->options, array_filter(array(
            'title' => $this->title,
            'icon' => $this->icon
        )));
    }
}