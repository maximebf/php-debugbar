<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\DataCollector;

/**
 * Indicates that a DataCollector provides some widgets to be added
 * to the debugbar
 */
interface WidgetProvider
{
    /**
     * Returns an array of Widget objects
     *
     * @return array
     */
    function getWidgets();
}
