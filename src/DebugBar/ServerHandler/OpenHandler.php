<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\ServerHandler;

use DebugBar\DebugBarException;

/**
 * Handler to list and open saved dataset
 */
class OpenHandler implements ServerHandlerInterface
{
    public function getName()
    {
        return 'open';
    }

    public function getCommandNames()
    {
        return array('find', 'get', 'clear');
    }

    public function find($request, $debugbar)
    {
        $max = 20;
        if (isset($request['max'])) {
            $max = $request['max'];
        }

        $offset = 0;
        if (isset($request['offset'])) {
            $offset = $request['offset'];
        }

        $filters = array();
        foreach (array('utime', 'datetime', 'ip', 'uri', 'method') as $key) {
            if (isset($request[$key])) {
                $filters[$key] = $request[$key];
            }
        }

        return $debugbar->getStorage()->find($filters, $max, $offset);
    }

    public function get($request, $debugbar)
    {
        if (!isset($request['id'])) {
            throw new DebugBarException("Missing 'id' parameter in 'get' operation");
        }
        return $debugbar->getStorage()->get($request['id']);
    }

    public function clear($request, $debugbar)
    {
        $debugbar->getStorage()->clear();
        return array('success' => true);
    }
}
