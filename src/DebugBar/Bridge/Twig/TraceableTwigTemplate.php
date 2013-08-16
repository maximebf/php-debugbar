<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\Bridge\Twig;

use Twig_TemplateInterface;
use Twig_Template;

/**
 * Wraps a Twig_Template to add profiling features
 */
class TraceableTwigTemplate implements Twig_TemplateInterface
{
    protected $template;

    /**
     * @param TraceableTwigEnvironment $env
     * @param Twig_Template $template
     */
    public function __construct(TraceableTwigEnvironment $env, Twig_Template $template)
    {
        $this->env = $env;
        $this->template = $template;
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateName()
    {
        return $this->template->getTemplateName();
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironment()
    {
        return $this->template->getEnvironment();
    }

    /**
     * {@inheritDoc}
     */
    public function getParent(array $context)
    {
        return $this->template->getParent($context);
    }

    /**
     * {@inheritDoc}
     */
    public function isTraitable()
    {
        return $this->template->isTraitable();
    }

    /**
     * {@inheritDoc}
     */
    public function displayParentBlock($name, array $context, array $blocks = array())
    {
        $this->template->displayParentBlock($name, $context, $blocks);
    }

    /**
     * {@inheritDoc}
     */
    public function displayBlock($name, array $context, array $blocks = array())
    {
        $this->displayBlock($name, $context, $blocks);
    }

    /**
     * {@inheritDoc}
     */
    public function renderParentBlock($name, array $context, array $blocks = array())
    {
        return $this->template->renderParentBlock($name, $context, $blocks);
    }

    /**
     * {@inheritDoc}
     */
    public function renderBlock($name, array $context, array $blocks = array())
    {
        return $this->template->renderBlock($name, $context, $blocks);
    }

    /**
     * {@inheritDoc}
     */
    public function hasBlock($name)
    {
        return $this->template->hasBlock($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockNames()
    {
        return $this->template->getBlockNames();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlocks()
    {
        return $this->template->getBlocks();
    }

    /**
     * {@inheritDoc}
     */
    public function display(array $context, array $blocks = array())
    {
        $start = microtime(true);
        $this->template->display($context, $blocks);
        $end = microtime(true);

        if ($timeDataCollector = $this->env->getTimeDataCollector()) {
            $name = sprintf("twig.render(%s)", $this->template->getTemplateName());
            $timeDataCollector->addMeasure($name, $start, $end);
        }

        $this->env->addRenderedTemplate(array(
            'name' => $this->template->getTemplateName(),
            'render_time' => $end - $start
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function render(array $context)
    {
        $level = ob_get_level();
        ob_start();
        try {
            $this->display($context);
        } catch (Exception $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }

            throw $e;
        }

        return ob_get_clean();
    }

    /**
     * {@inheritDoc}
     */
    public static function clearCache()
    {
        $this->template->clearCache();
    }
}
