<?php

namespace DebugBar\Tests;

use DebugBar\DebugBar;
use DebugBar\DebugBarException;
use DebugBar\Tests\DataCollector\MockCollector;
use DebugBar\Tests\Storage\MockStorage;
use DebugBar\RandomRequestIdGenerator;
use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Panther\DomCrawler\Link;
use Symfony\Component\Panther\PantherTestCase;

abstract class AbstractBrowserTest extends PantherTestCase
{
    public function isTabActive(Crawler $crawler, $tab)
    {
        $node = $crawler->filter('a.phpdebugbar-tab[data-collector="'.$tab.'"]');

        return strpos($node->attr('class'), 'phpdebugbar-active"') !== false;
    }

    public function getTabLink(Crawler $crawler, $tab): Link
    {
        return $crawler->filter('a.phpdebugbar-tab[data-collector="'.$tab.'"]')->link();
    }
}