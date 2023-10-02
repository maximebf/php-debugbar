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
use DebugBar\DataFormatter\HasDataFormatter;

/**
 * Provides a way to log messages
 */
class MessagesCollector extends AbstractLogger implements DataCollectorInterface, MessagesAggregateInterface, Renderable, AssetProvider
{
    use HasDataFormatter;

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
    public function addMessage($message, $label = 'info', $isString = true)
    {
        $messageText = $message;
        $messageHtml = null;
        if (!is_string($message)) {
            // Send both text and HTML representations; the text version is used for searches
            $messageText = $this->getDataFormatter()->formatVar($message);
            if ($this->isHtmlVarDumperUsed()) {
                $messageHtml = $this->getVarDumper()->renderVar($message);
            }
            $isString = false;
        }
        $this->messages[] = array(
            'message' => $messageText,
            'message_html' => $messageHtml,
            'is_string' => $isString,
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
     * @return array
     */
    public function getMessages()
    {
        $messages = $this->messages;
        foreach ($this->aggregates as $collector) {
            $msgs = array_map(function ($m) use ($collector) {
                $m['collector'] = $collector->getName();
                return $m;
            }, $collector->getMessages());
            $messages = array_merge($messages, $msgs);
        }

        // sort messages by their timestamp
        usort($messages, function ($a, $b) {
            if ($a['time'] === $b['time']) {
                return 0;
            }
            return $a['time'] < $b['time'] ? -1 : 1;
        });

        return $messages;
    }

    /**
     * @param $level
     * @param $message
     * @param array $context
     */
    public function log($level, $message, array $context = array()): void
    {
        // For string messages, interpolate the context following PSR-3
        if (is_string($message)) {
            $message = $this->interpolate($message, $context);
        }
        $this->addMessage($message, $level);
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param $message
     * @param array $context
     * @return string
     */
    function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * Deletes all messages
     */
    public function clear()
    {
        $this->messages = array();
    }

    /**
     * @return array
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getAssets() {
        return $this->isHtmlVarDumperUsed() ? $this->getVarDumper()->getAssets() : array();
    }

    /**
     * @return array
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
