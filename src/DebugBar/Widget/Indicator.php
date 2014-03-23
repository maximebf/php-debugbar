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
 * Represents an indicator in the client side debugbar
 */
class Indicator extends AbstractWidget
{
    const POSITION_LEFT = 'left';
    const POSITION_RIGHT = 'right';

    protected $className = 'PhpDebugBar.DebugBar.Indicator';

    protected $tooltip;

    protected $position = self::POSITION_RIGHT;

    public function __construct($icon, $dataMap, $defaultValue = '', $tooltip = null, $position = self::POSITION_RIGHT)
    {
        $this->icon = $icon;
        $this->mapping = $dataMap;
        $this->defaultValue = $defaultValue;
        $this->tooltip = $tooltip;
        $this->position = $position;
    }

    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;
        return $this;
    }

    public function getTooltip()
    {
        return $this->tooltip;
    }

    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getConstructorOptions()
    {
        return array_merge(parent::getConstructorOptions(), array_filter(array(
            'tooltip' => $this->tooltip
        )));
    }
}