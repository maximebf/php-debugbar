<?php

namespace DebugBar\Bridge\Twig;

use DebugBar\DataFormatter\HasDataFormatter;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Dump variables using debugbar DataFormatter
 *
 * @package DebugBar\Bridge\Twig
 */
class DumpTwigExtension extends AbstractExtension
{
    use HasDataFormatter;

    /**
     * @var string
     */
    protected $functionName;

    /**
     * Create a new auth extension.
     *
     * @param string $functionName
     */
    public function __construct($functionName = 'dump')
    {
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
                [$this, 'dump'],
                ['is_safe' => ['html'], 'needs_context' => true, 'needs_environment' => true]
            ),
        ];
    }

    /**
     * Based on Twig_Extension_Debug / twig_var_dump
     *
     * @param Environment $env
     * @param $context
     *
     * @return string
     */
    public function dump(Environment $env, $context)
    {
        if (!$env->isDebug()) {
            return;
        }

        $output = '';

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
            $output .= $this->formatVar($data);
        } else {
            for ($i = 2; $i < $count; $i++) {
                $output .= $this->formatVar(func_get_arg($i));
            }
        }

        if ($this->isHtmlVarDumperUsed()) {
            return $output;
        }

        return '<pre>' . $output . '</pre>';
    }
}
