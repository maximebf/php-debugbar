<?php

namespace DebugBar\DataCollector;

class RequestDataCollector extends DataCollector
{
    public function getName()
    {
        return 'request';
    }

    public function collect()
    {
        $vars = array('_GET', '_POST', '_SESSION', '_COOKIE', '_SERVER');
        $data = array();

        foreach ($vars as $var) {
            if (isset($GLOBALS[$var])) {
                $data["$" . $var] = $this->varToString($GLOBALS[$var]);
            }
        }

        return $data;
    }
}
