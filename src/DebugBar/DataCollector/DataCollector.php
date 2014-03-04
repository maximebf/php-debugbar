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

use DebugBar\DataFormater\DataFormaterInterface;
use DebugBar\DataFormater\DataFormater;

/**
 * Abstract class for data collectors
 */
abstract class DataCollector implements DataCollectorInterface
{
    private static $defaultDataFormater;

    protected $dataFormater;

    /**
     * Sets the default data formater instance used by all collectors subclassing this class
     *
     * @param DataFormaterInterface $formater
     */
    public static function setDefaultDataFormater(DataFormaterInterface $formater)
    {
        self::$defaultDataFormater = $formater;
    }

    /**
     * Returns the default data formater
     *
     * @return DataFormaterInterface
     */
    public static function getDefaultDataFormater()
    {
        if (self::$defaultDataFormater === null) {
            self::$defaultDataFormater = new DataFormater();
        }
        return self::$defaultDataFormater;
    }

    /**
     * Sets the data formater instance used by this collector
     *
     * @param DataFormaterInterface $formater
     */
    public function setDataFormater(DataFormaterInterface $formater)
    {
        $this->dataFormater = $formater;
        return $this;
    }

    public function getDataFormater()
    {
        if ($this->dataFormater === null) {
            $this->dataFormater = self::getDefaultDataFormater();
        }
        return $this->dataFormater;
    }

    /**
     * @deprecated
     */
    public function formatVar($var)
    {
        return $this->getDataFormater()->formatVar($var);
    }

    /**
     * @deprecated
     */
    public function formatDuration($seconds)
    {
        return $this->getDataFormater()->formatDuration($seconds);
    }

    /**
     * @deprecated
     */
    public function formatBytes($size, $precision = 2)
    {
        return $this->getDataFormater()->formatBytes($size, $precision);
    }
}
