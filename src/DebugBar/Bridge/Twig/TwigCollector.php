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

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Collects data about rendered templates
 *
 * http://twig.sensiolabs.org/
 *
 * Your Twig_Environment object needs to be wrapped in a
 * TraceableTwigEnvironment object
 *
 * <code>
 * $env = new TraceableTwigEnvironment(new Twig_Environment($loader));
 * $debugbar->addCollector(new TwigCollector($env));
 * </code>
 */
class TwigCollector extends DataCollector implements Renderable
{
    public function __construct(TraceableTwigEnvironment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $templates = array();
        $accuRenderTime = 0;

        foreach ($this->twig->getRenderedTemplates() as $tpl) {
            $accuRenderTime += $tpl['render_time'];
            $templates[] = array(
                'name' => $tpl['name'],
                'render_time' => $tpl['render_time'],
                'render_time_str' => $this->formatDuration($tpl['render_time'])
            );
        }

        return array(
            'nb_templates' => count($templates),
            'templates' => $templates,
            'accumulated_render_time' => $accuRenderTime,
            'accumulated_render_time_str' => $this->formatDuration($accuRenderTime)
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'twig';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return array(
            'twig' => array(
                'icon' => 'leaf',
                'widget' => 'PhpDebugBar.Widgets.TemplatesWidget',
                'map' => 'twig',
                'default' => '[]'
            ),
            'twig:badge' => array(
                'map' => 'twig.nb_templates',
                'default' => 0
            )
        );
    }
}
