<?php

namespace DebugBar\DataCollector;

class PhpInfoCollector extends DataCollector
{
    public function getName()
    {
        return 'php';
    }

    public function collect()
    {
        return array(
            'version' => PHP_VERSION
        );
    }
}
