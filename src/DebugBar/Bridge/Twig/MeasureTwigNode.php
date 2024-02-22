<?php

namespace DebugBar\Bridge\Twig;

use DebugBar\Bridge\Twig\MeasureTwigExtension;
use Twig\Compiler;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Node\Node;

/**
 * Represents a measure node.
 * Based on Symfony\Bridge\Twig\Node\StopwatchNode
 */
class MeasureTwigNode extends Node
{
    public function __construct(
        Node $name,
        $body,
        AssignNameExpression $var,
        $lineno = 0,
        $tag = null
    ) {
        parent::__construct(['body' => $body, 'name' => $name, 'var' => $var], [], $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write('')
            ->subcompile($this->getNode('var'))
            ->raw(' = ')
            ->subcompile($this->getNode('name'))
            ->write(";\n")
            ->write("\$this->env->getExtension('".MeasureTwigExtension::class."')->startMeasure(")
            ->subcompile($this->getNode('var'))
            ->raw(");\n")
            ->subcompile($this->getNode('body'))
            ->write("\$this->env->getExtension('".MeasureTwigExtension::class."')->stopMeasure(")
            ->subcompile($this->getNode('var'))
            ->raw(");\n");
    }
}
