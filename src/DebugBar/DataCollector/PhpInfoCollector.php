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
 * Collects info about PHP
 */
class PhpInfoCollector extends DataCollector
{
    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        return array(
            'version' => PHP_VERSION
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'php';
    }
}
