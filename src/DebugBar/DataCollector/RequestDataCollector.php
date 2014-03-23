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

use DebugBar\Widget\VariableListTab;

/**
 * Collects info about the current request
 */
class RequestDataCollector extends DataCollector implements WidgetProvider
{
    public function collect()
    {
        $vars = array('_GET', '_POST', '_SESSION', '_COOKIE', '_SERVER');
        $data = array();

        foreach ($vars as $var) {
            if (isset($GLOBALS[$var])) {
                $data["$" . $var] = $this->getDataFormatter()->formatVar($GLOBALS[$var]);
            }
        }

        return $data;
    }

    public function getName()
    {
        return 'request';
    }

    public function getWidgets()
    {
        return array(
            "request" => new VariableListTab("tags", "request")
        );
    }
}
