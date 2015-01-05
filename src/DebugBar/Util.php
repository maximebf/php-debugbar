<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2014 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar;

use DebugBar\DataFormatter\DataFormatter;
use DebugBar\DataFormatter\DataFormatterInterface;

/**
 * Debugbar Utility Helper
 *
 * Helps provide common functions with a static interface
 *
 * <code>
 *     if (!Util::isUtf8($string)) {
 *          $string = Util::toUtf8($string);
 *     }
 * </code>
 */
class Util
{

    private static $dataFormatter;

    /**
     * Check if a string is UTF-8
     *
     * @param  string $str
     * @return bool
     */
    public static function isUtf8($str)
    {
        if (function_exists('mb_check_encoding')) {
            return mb_check_encoding($str, 'UTF-8');
        }

        return (bool) preg_match('//u', $str);
    }

    /**
     * Convert a string to UTF-8
     *
     * @param  string $str
     * @return string
     */
    public static function toUtf8($str)
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        }

        return iconv('UTF-8', 'UTF-8//IGNORE', $str);
    }

    /**
     * Escape a string so it is safe to use in HTML
     *
     * @param  string $str
     * @return string
     */
    public static function escape($str)
    {
        return htmlentities($str, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * Transforms a PHP variable to a string representation
     *
     * @param  mixed $data
     * @return string
     */
    public static function formatVar($data)
    {
        return self::getDataFormatter()->formatVar($data);
    }

    /**
     * Transforms a duration in seconds in a readable string
     *
     * @param  float $seconds
     * @return string
     */
    public static function formatDuration($seconds)
    {
        return self::getDataFormatter()->formatDuration($seconds);
    }

    /**
     * Transforms a size in bytes to a human readable string
     *
     * @param  string $size
     * @param  integer $precision
     * @return string
     */
    public static function formatBytes($size, $precision = 2)
    {
        return self::getDataFormatter()->formatBytes($size, $precision);
    }

    /**
     * Get the DataFormatter instance.
     *
     * @return DataFormatter
     */
    public static function getDataFormatter()
    {
        if (self::$dataFormatter === null) {
            self::$dataFormatter = new DataFormatter();
        }

        return self::$dataFormatter;
    }

    /**
     * Set the DatFormatter for the Util class
     *
     * @param DataFormatterInterface $dataFormatter
     */
    public static function setDataFormatter(DataFormatterInterface $dataFormatter)
    {
        self::$dataFormatter = $dataFormatter;
    }
}
