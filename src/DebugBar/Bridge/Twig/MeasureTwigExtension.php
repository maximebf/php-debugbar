<?php

namespace DebugBar\Bridge\Twig;

use Twig\Extension\AbstractExtension;

/**
 * Access debugbar timeline measure in your Twig templates.
 * Based on Symfony\Bridge\Twig\Extension\StopwatchExtension
 *
 * @package DebugBar\Bridge\Twig
 */
class MeasureTwigExtension extends AbstractExtension
{
    /**
     * @var \DebugBar\DataCollector\TimeDataCollector |null
     */
    protected $timeCollector;

    /**
     * @var string
     */
    protected $tagName;

    /**
     * Create a new auth extension.
     *
     * @param \DebugBar\DebugBar|null $debugbar
     * @param string $tagName
     */
    public function __construct($debugbar, $tagName = 'measure')
    {
        if ($debugbar && $debugbar->hasCollector('time')) {
            $this->timeCollector = $debugbar['time'];
        }

        $this->tagName = $tagName;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return static::class;
    }

    /**
     * @return \Twig\TokenParser\TokenParserInterface[]
     */
    public function getTokenParsers()
    {
        return [
            /*
             * {% measure foo %}
             * Some stuff which will be recorded on the timeline
             * {% endmeasure %}
             */
            new MeasureTwigTokenParser(!is_null($this->timeCollector), $this->tagName),
        ];
    }

    public function startMeasure(...$arg)
    {
        if (!$this->timeCollector) {
            return;
        }

        $this->timeCollector->startMeasure(...$arg);
    }

    public function stopMeasure(...$arg)
    {
        if (!$this->timeCollector) {
            return;
        }

        $this->timeCollector->stopMeasure(...$arg);
    }
}
