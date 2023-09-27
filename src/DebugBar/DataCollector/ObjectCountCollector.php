<?php

namespace DebugBar\DataCollector;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\DataCollectorInterface;
use DebugBar\DataCollector\Renderable;

/**
 * Collector for hit counts.
 */
class ObjectCountCollector extends DataCollector implements DataCollectorInterface, Renderable
{
    /** @var string */
    private $name;
    /** @var string */
    private $icon;
    /** @var int */
    protected $classCount = 0;
    /** @var array */
    protected $classList = [];

    /**
     * @param string $name
     * @param string $icon
     */
    public function __construct($name = 'counter', $icon = 'cubes')
    {
        $this->name = $name;
        $this->icon = $icon;
    }

    /**
     * @param string|mixed $class
     * @param int $count
     */
    public function countClass($class, $count = 1) {
        if (! is_string($class)) {
            $class = get_class($class);
        }

        $this->classList[$class] = ($this->classList[$class] ?? 0) + $count;
        $this->classCount += $count;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        arsort($this->classList, SORT_NUMERIC);

        if (! $this->getXdebugLinkTemplate()) {
            return ['data' => $this->classList, 'count' => $this->classCount, 'is_counter' => true];
        }

        $data = [];
        foreach ($this->classList as $class => $count) {
            $reflector = class_exists($class) ? new \ReflectionClass($class) : null;

            if ($reflector && $link = $this->getXdebugLink($reflector->getFileName())) {
                $data[$class . '<a href="' . $link['url'] . '" class="phpdebugbar-widgets-editor-link"></a>'] = $count;
            } else {
                $data[$class] = $count;
            }
        }

        return ['data' => $data, 'count' => $this->classCount, 'is_counter' => true];
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

        return [
            "$name" => [
                'icon' => $this->icon,
                'widget' => 'PhpDebugBar.Widgets.HtmlVariableListWidget',
                'map' => "$name.data",
                'default' => '{}'
            ],
            "$name:badge" => [
                'map' => "$name.count",
                'default' => 0
            ]
        ];
    }
}
