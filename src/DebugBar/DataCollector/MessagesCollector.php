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

use Psr\Log\AbstractLogger;

/**
 * Provides a way to log messages
 */
class MessagesCollector extends AbstractLogger implements DataCollectorInterface, MessagesAggregateInterface, Renderable
{
    protected $name;

    protected $messages = array();

    protected $aggregates = array();

    /**
     * @param string $name
     */
    public function __construct($name = 'messages')
    {
        $this->name = $name;
    }

    /**
     * Adds a message
     *
     * A message can be anything from an object to a string
     * 
     * @param mixed $message
     * @param string $label
     */
    public function addMessage($message, $label = 'info')
    {
        $this->messages[] = array(
            'message' => print_r($message, true),
            'is_string' => is_string($message),
            'label' => $label,
            'time' => microtime(true)
        );
    }

    /**
     * Aggregates messages from other collectors
     * 
     * @param MessagesAggregateInterface $messages
     */
    public function aggregate(MessagesAggregateInterface $messages)
    {
        $this->aggregates[] = $messages;
    }

    /**
     * {@inheritDoc}
     */
    public function getMessages()
    {
        $messages = $this->messages;
        foreach ($this->aggregates as $collector) {
            $msgs = array_map(function($m) use ($collector) {
                $m['collector'] = $collector->getName();
                return $m;
            }, $collector->getMessages());
            $messages = array_merge($messages, $msgs);
        }

        // sort messages by their timestamp
        usort($messages, function($a, $b) {
            if ($a['time'] === $b['time']) {
                return 0;
            }
            return $a['time'] < $b['time'] ? -1 : 1;
        });

        return $messages;
    }

    /**
     * {@inheritDoc}
     */
    public function log($level, $message, array $context = array())
    {
        $this->addMessage($message, $level);
    }

    /**
     * Deletes all messages
     */
    public function clear()
    {
        $this->messages = array();
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $messages = $this->getMessages();
        return array(
            'count' => count($messages),
            'messages' => $messages
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        $name = $this->getName();
        return array(
            "$name" => array(
                'icon' => 'list-alt',
                "widget" => "PhpDebugBar.Widgets.MessagesWidget",
                "map" => "$name.messages",
                "default" => "[]"
            ),
            "$name:badge" => array(
                "map" => "$name.count",
                "default" => "null"
            )
        );
    }
}
