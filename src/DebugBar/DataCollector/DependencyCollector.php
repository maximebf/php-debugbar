<?php

namespace DebugBar\DataCollector;

class DependencyCollector extends DataCollector
{
    public function getName()
    {
        return 'dependency';
    }

    public function collect()
    {
        return array();
    }
}
