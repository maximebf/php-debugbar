<?php

namespace DebugBar\Tests;

use DebugBar\DebugBar;
use DebugBar\DebugBarException;
use DebugBar\Tests\DataCollector\MockCollector;
use DebugBar\Tests\Storage\MockStorage;
use DebugBar\RandomRequestIdGenerator;
use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\PantherTestCase;

class BasicBrowserTest extends PantherTestCase
{
    public function testDebugbar(): void
    {
        // Start demo
        $client = static::createPantherClient();
        $client->request('GET', '/demo');

        // Wait for Debugbar to load
        $crawler = $client->waitFor('.phpdebugbar-body');

        $firstTab = $crawler->filter('a.phpdebugbar-tab')->link();
        $client->click($firstTab);

        $crawler = $client->waitForVisibility('.phpdebugbar-widgets-messages .phpdebugbar-widgets-list');

        $messages = $crawler->filter('.phpdebugbar-widgets-messages .phpdebugbar-widgets-value')
            ->each(fn(WebDriverElement $node) => $node->getText());

        $this->assertEquals('hello', $messages[0]);
        $this->assertCount(4, $messages);

        $firstTab = $crawler->filter('a.phpdebugbar-tab')->link();
        $client->click($firstTab);
        $client->waitForInvisibility('.phpdebugbar-widgets-messages .phpdebugbar-widgets-list');

    }
}