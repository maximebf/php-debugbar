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
    /**
     * @var string
     */
    protected $extName;

    public function __construct(
        Node $name,
        $body,
        AssignNameExpression $var,
        $lineno = 0,
        $tag = null,
        $extName = null
    ) {
        parent::__construct(['body' => $body, 'name' => $name, 'var' => $var], [], $lineno, $tag);
        $this->extName = $extName ?: MeasureTwigExtension::class;
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
            ->write("\$this->env->getExtension('".$this->extName."')->startMeasure(")
            ->subcompile($this->getNode('var'))
            ->raw(");\n")
            ->subcompile($this->getNode('body'))
            ->write("\$this->env->getExtension('".$this->extName."')->stopMeasure(")
            ->subcompile($this->getNode('var'))
            ->raw(");\n");
    }
}
