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
 * Collects info about memory usage
 */
class MemoryCollector extends DataCollector implements Renderable
{
    protected $peakUsage = 0;

    /**
     * Returns the peak memory usage
     * 
     * @return integer
     */
    public function getPeakUsage()
    {
        return $this->peakUsage;
    }

    /**
     * Updates the peak memory usage value
     */
    public function updatePeakUsage()
    {
        $this->peakUsage = memory_get_peak_usage(true);
    }

    /**
     * Transforms a size in bytes to a human readable string
     * 
     * @param string $size
     * @param integer $precision
     * @return string
     */
    public function toReadableString($size, $precision = 2)
    {
        $base = log($size) / log(1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');   
        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)]; 
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $this->updatePeakUsage();
        return array(
            'peak_usage' => $this->peakUsage,
            'peak_usage_str' => $this->toReadableString($this->peakUsage)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'memory';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return array(
            "memory" => array(
                "icon" => "cogs", 
                "tooltip" => "Memory Usage", 
                "map" => "peak_usage_str", 
                "default" => "'0B'"
            )
        );
    }
}
