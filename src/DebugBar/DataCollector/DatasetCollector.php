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
 * Collects array data
 */
class DatasetCollector extends DataCollector implements Renderable, AssetProvider
{


    /**
     * @return array
     */
    public function collect()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Datasets';
    }

    /**
     * @return array
     */
    public function getWidgets()
    {
        return array(
            "__datasets" => array(
                "icon" => "history",
                "title" => "Requests",
                "widget" => 'PhpDebugBar.Widgets.DatasetListWidget',
                "default" => "{}",
                "position" => "right"
            )
        );
    }

    function getAssets()
    {
        return [];
    }
}
