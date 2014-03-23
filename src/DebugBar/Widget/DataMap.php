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
 * Represents a mapping for the client side debugbar
 */
class DataMap
{
    protected $mapping;

    protected $defaultValue;

    public function __construct($mapping, $defaultValue = "null")
    {
        $this->mapping = $mapping;
        $this->defaultValue = $defaultValue;
    }

    public function setMapping($map)
    {
        $this->mapping = $map;
        return $this;
    }

    public function getMapping()
    {
        return $this->mapping;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}