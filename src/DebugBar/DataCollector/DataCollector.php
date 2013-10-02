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
 * Abstract class for data collectors
 */
abstract class DataCollector implements DataCollectorInterface
{
    /**
     * Transforms a PHP variable to a string representation
     * 
     * @param mixed $var
     * @return string
     */
    public function formatVar($var)
    {
        $var = $this->flattenVar($var);
        return print_r($var, true);
    }

    /**
     * Flatten a var recursively
     *
     * @param $var
     * @return mixed
     */
    public function flattenVar($var){
        if (is_array($var)) {
            foreach ($var as &$v) {
                $v = $this->flattenVar($v);
            }
        } else if (is_object($var)) {
            $var = "Object(" . get_class($var) . ")";
        }
        return $var;
    }

    /**
     * Transforms a duration in seconds in a readable string
     * 
     * @param float $seconds
     * @return string
     */
    public function formatDuration($seconds)
    {
        if ($seconds < 0.001) {
            return round($seconds * 1000000) . 'μs';
        } else if ($seconds < 1) {
            return round($seconds * 1000, 2) . 'ms';
        }
        return round($seconds, 2) . 's';
    }

    /**
     * Transforms a size in bytes to a human readable string
     * 
     * @param string $size
     * @param integer $precision
     * @return string
     */
    public function formatBytes($size, $precision = 2)
    {
        if ($size === 0 || $size === null) {
            return "0B";
        }
        $base = log($size) / log(1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }
}
