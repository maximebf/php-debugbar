<?php

namespace DebugBar\DataCollector;

interface DataCollectorInterface
{
    function getName();

    function collect();
}
