<?php

namespace DebugBar\DataCollector;

class MemoryCollector extends DataCollector
{
    protected $peakUsage = 0;

    public function getName()
    {
        return 'memory';
    }

    public function getPeakUsage()
    {
        return $this->peakUsage;
    }

    public function updatePeakUsage()
    {
        $this->peakUsage = memory_get_peak_usage(true);
    }

    public function toReadableString($size, $precision = 2)
    {
        $base = log($size) / log(1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');   
        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)]; 
    }

    public function collect()
    {
        $this->updatePeakUsage();
        return array(
            'peak_usage' => $this->peakUsage,
            'peak_usage_str' => $this->toReadableString($this->peakUsage)
        );
    }
}
