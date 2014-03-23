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
use CacheCache\LoggingBackend;
use Monolog\Logger;
use DebugBar\Bridge\MonologCollector;
use DebugBar\DataCollector\AssetProvider;
use DebugBar\ServerHandler\ServerHandlerFactoryInterface;
use DebugBar\Widget\CacheTab;
use DebugBar\Widget\DataMap;

/**
 * Collects CacheCache operations
 *
 * http://maximebf.github.io/CacheCache/
 *
 * Example:
 * <code>
 * $debugbar->addCollector(new CacheCacheCollector(CacheManager::get('default')));
 * // or
 * $debugbar->addCollector(new CacheCacheCollector());
 * $debugbar['cache']->addCache(CacheManager::get('default'));
 * </code>
 */
class CacheCacheCollector extends MonologCollector implements AssetProvider, ServerHandlerFactoryInterface
{
    protected $logger;

    protected $caches = array();

    public function __construct(Cache $cache = null, Logger $logger = null, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct(null, $level, $bubble);

        if ($logger === null) {
            $logger = new Logger('Cache');
        }
        $this->logger = $logger;

        if ($cache !== null) {
            $this->addCache($cache);
        }
    }

    /**
     * Adds another cache backend which operations should be logged
     *
     * @param Cache $cache
     */
    public function addCache(Cache $cache)
    {
        $backend = $cache->getBackend();
        if (!($backend instanceof LoggingBackend)) {
            $backend = new LoggingBackend($backend, $this->logger);
        }
        $cache->setBackend($backend);
        $this->addLogger($backend->getLogger());
        $this->caches[] = $cache;
        return $this;
    }

    public function getName()
    {
        return 'cache';
    }

    public function getWidgets()
    {
        $name = $this->getName();
        return array(
            $name => new CacheTab("suitcase", "$name.records"),
            "$name:badge" => new DataMap("$name.count")
        );
    }

    public function getAssets()
    {
        return array(
            'css' => 'widgets/cache/widget.css',
            'js' => 'widgets/cache/widget.js'
        );
    }

    public function getServerHandler()
    {
        return new CacheCacheHandler($this->caches);
    }
}
