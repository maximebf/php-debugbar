<?php

namespace DebugBar\DataCollector;

class TimeCollector extends DataCollector
{

    protected $requestStartTime;

    protected $requestEndTime;

    protected $measures = array();

    public function __construct($requestStartTime = null)
    {
        if ($requestStartTime === null) {
            if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
                $requestStartTime = $_SERVER['REQUEST_TIME_FLOAT'];
            } else {
                $requestStartTime = microtime(true);
            }
        }
        $this->requestStartTime = $requestStartTime;
    }

    public function startMeasure($name, $label = null)
    {
        $start = microtime(true);
        $this->measures[$name] = array(
            'label' => $label ?: $name,
            'start' => $start,
            'relative_start' => $start - $this->requestStartTime
        );
    }

    public function stopMeasure($name)
    {
        $end = microtime(true);
        $this->measures[$name]['end'] = $end;
        $this->measures[$name]['relative_end'] = $end - $this->requestEndTime;
        $this->measures[$name]['duration'] = $end - $this->measures[$name]['start'];
        $this->measures[$name]['duration_str'] = $this->toReadableString($this->measures[$name]['duration']);
    }

    public function getRequestStartTime()
    {
        return $this->requestStartTime;
    }

    public function getRequestEndTime()
    {
        return $this->requestEndTime;
    }

    public function getRequestDuration()
    {
        if ($this->requestEndTime !== null) {
            return $this->requestEndTime - $this->requestStartTime;
        }
        return microtime(true) - $this->requestStartTime;
    }

    public function getMeasures()
    {
        return $this->measures;
    }

    public function toReadableString($value)
    {
        return round($value / 1000) . 'ms';
    }

    public function getName()
    {
        return 'time';
    }

    public function collect()
    {
        $this->requestEndTime = microtime(true);
        foreach ($this->measures as $name => $data) {
            if (!isset($data['end'])) {
                $this->stopMeasure($name);
            }
        }

        return array(
            'start' => $this->requestStartTime,
            'end' => $this->requestEndTime,
            'duration' => $this->getRequestDuration(),
            'duration_str' => $this->toReadableString($this->getRequestDuration()),
            'measures' => array_values($this->measures)
        );
    }
}
