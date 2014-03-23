<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\Bridge\CacheCache;

use CacheCache\Cache;
use DebugBar\ServerHandler\ServerHandlerInterface;

class CacheCacheHandler implements ServerHandlerInterface
{
    protected $caches = array();

    public function __construct(array $caches = array())
    {
        $this->caches = $caches;
    }

    public function addCache(Cache $cache)
    {
        $this->caches[] = $cache;
    }

    public function getName()
    {
        return 'cache';
    }

    public function getCommandNames()
    {
        return array('clear', 'clearKey');
    }

    public function clear($request, $debugbar)
    {
        foreach ($this->caches as $cache) {
            $cache->flushAll();
        }
    }

    public function clearKey($request, $debugbar)
    {
        foreach ($this->caches as $cache) {
            $cache->delete($request['key']);
        }
    }
}
