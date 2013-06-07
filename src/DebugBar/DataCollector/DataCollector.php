<?php

namespace DebugBar\DataCollector;

abstract class DataCollector implements DataCollectorInterface
{
    public function varToString($var)
    {
        return print_r($var, true);
    }
}
