<?php

namespace DebugBar\DataCollector;

class MessagesCollector extends DataCollector
{
    protected $messages = array();

    public function addMessage($message, $label = 'info')
    {
        $this->messages[] = array(
            'message' => $this->varToString($message),
            'is_string' => is_string($message),
            'label' => $label,
            'time' => microtime(true),
            'memory_usage' => memory_get_usage(),
            'backtrace' => debug_backtrace()
        );
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getName()
    {
        return 'messages';
    }

    public function collect()
    {
        return $this->messages;
    }
}
