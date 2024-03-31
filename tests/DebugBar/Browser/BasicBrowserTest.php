<?php

namespace DebugBar\Tests;

use DebugBar\DebugBar;
use DebugBar\DebugBarException;
use DebugBar\Tests\DataCollector\MockCollector;
use DebugBar\Tests\Storage\MockStorage;
use DebugBar\RandomRequestIdGenerator;
use Facebook\WebDriver\WebDriverElement;
use Symfony\Component\Panther\PantherTestCase;

class BasicBrowserTest extends AbstractBrowserTest
{
    public function testDebugbarTab(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/demo/');

        // Wait for Debugbar to load
        $crawler = $client->waitFor('.phpdebugbar-body');

        $client->click($this->getTabLink($crawler, 'messages'));

        $crawler = $client->waitForVisibility('.phpdebugbar-panel[data-collector=messages] .phpdebugbar-widgets-list');

        $messages = $crawler->filter('.phpdebugbar-panel[data-collector=messages] .phpdebugbar-widgets-value')
            ->each(function(WebDriverElement $node){
                return $node->getText();
            });

        $this->assertEquals('hello', $messages[0]);
        $this->assertCount(4, $messages);

        // Close it again
        $client->click($this->getTabLink($crawler, 'messages'));
        $client->waitForInvisibility('.phpdebugbar-panel[data-collector=messages] .phpdebugbar-widgets-list');
    }

    public function testDebugbarAjax(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/demo/');

        // Wait for Debugbar to load
        $crawler = $client->waitFor('.phpdebugbar-body');

        $client->click($this->getTabLink($crawler, 'messages'));

        $crawler = $client->waitForVisibility('.phpdebugbar-widgets-messages .phpdebugbar-widgets-list');

        $crawler->selectLink('load ajax content')->click();
        $client->waitForElementToContain('.phpdebugbar-panel[data-collector=messages]', 'hello from ajax');
        $client->waitForElementToContain('.phpdebugbar-datasets-switcher', 'ajax.php');

        $messages = $crawler->filter('.phpdebugbar-panel[data-collector=messages] .phpdebugbar-widgets-value')
            ->each(function(WebDriverElement $node){
                return $node->getText();
            });

        $this->assertEquals('hello from ajax', $messages[0]);

        $crawler->selectLink('load ajax content with exception')->click();

        $client->click($this->getTabLink($crawler, 'exceptions'));

        $client->waitForElementToContain('.phpdebugbar-datasets-switcher', 'ajax_exception.php');
        $client->waitForElementToContain('.phpdebugbar-panel[data-collector=exceptions] .phpdebugbar-widgets-message', 'Something failed!');
    }

}