<?php

namespace DebugBar\Bridge\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Debug messages to debugbar in your Twig templates.
 *
 * @package DebugBar\Bridge\Twig
 */
class DebugTwigExtension extends AbstractExtension
{
    /**
     * @var \DebugBar\DataCollector\MessagesCollector|null
     */
    protected $messagesCollector;

    /**
     * @var string
     */
    protected $functionName;

    /**
     *
     * @param \DebugBar\DataCollector\MessagesCollector|null $app
     * @param string $functionName
     */
    public function __construct($messagesCollector, $functionName = 'debug')
    {
        $this->messagesCollector = $messagesCollector;
        $this->functionName = $functionName;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return static::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                $this->functionName,
                [$this, 'debug'],
                ['needs_context' => true, 'needs_environment' => true]
            ),
        ];
    }

    /**
     * Based on Twig_Extension_Debug / twig_var_dump
     *
     * @param Environment $env
     * @param $context
     */
    public function debug(Environment $env, $context)
    {
        if (!$env->isDebug() || !$this->messagesCollector) {
            return;
        }

        $count = func_num_args();
        if (2 === $count) {
            $data = [];
            foreach ($context as $key => $value) {
                if (is_object($value)) {
                    if (method_exists($value, 'toArray')) {
                        $data[$key] = $value->toArray();
                    } else {
                        $data[$key] = "Object (" . get_class($value) . ")";
                    }
                } else {
                    $data[$key] = $value;
                }
            }
            $this->messagesCollector->addMessage($data, 'debug');
        } else {
            for ($i = 2; $i < $count; $i++) {
                $this->messagesCollector->addMessage(func_get_arg($i), 'debug');
            }
        }

        return;
    }
}
