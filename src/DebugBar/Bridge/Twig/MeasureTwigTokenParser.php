<?php

namespace DebugBar\Bridge\Twig;

use Twig\Node\Expression\AssignNameExpression;
use Twig\Token;
use Twig\TokenParser\AbstractTokenParser;

/**
 * Token Parser for the measure tag.
 * Based on Symfony\Bridge\Twig\TokenParser\StopwatchTokenParser;
 */
class MeasureTwigTokenParser extends AbstractTokenParser
{
    /**
     * @var string
     */
    private $extName;

    /**
     * @var string
     */
    private $tagName;

    /**
     * @var bool
     */
    private $enabled;

    /**
     *
     * @param string $tagName
     */
    public function __construct($enabled, $tagName, $extName = null)
    {
        $this->enabled = $enabled;
        $this->tagName = $tagName;
        $this->extName = $extName;
    }

    public function parse(Token $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        // {% measure 'bar' %}
        $name = $this->parser->getExpressionParser()->parseExpression();

        $stream->expect(Token::BLOCK_END_TYPE);

        // {% endmeasure %}
        $body = $this->parser->subparse([$this, 'decideMeasureEnd'], true);
        $stream->expect(Token::BLOCK_END_TYPE);

        if ($this->enabled) {
            return new MeasureTwigNode(
                $name,
                $body,
                new AssignNameExpression($this->parser->getVarName(), $token->getLine()),
                $lineno,
                $this->getTag(),
                $this->extName
            );
        }

        return $body;
    }

    public function getTag()
    {
        return $this->tagName;
    }

    public function decideMeasureEnd(Token $token)
    {
        return $token->test('end'.$this->getTag());
    }
}
