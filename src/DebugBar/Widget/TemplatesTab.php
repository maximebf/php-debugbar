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

class TemplatesTab extends Tab
{
    public function __construct($icon, $mapping, $defaultValue = '[]')
    {
        parent::__construct(null, "PhpDebugBar.Widgets.TemplatesWidget",
            $mapping, $defaultValue, $icon);
    }
}