<?php

namespace DebugBar\Tests\Browser;

use DebugBar\Browser\Bridge\WebDriverElement;

class PdoTest extends AbstractBrowserTest
{
    public function testMonologCollector(): void
    {
        $client = static::createPantherClient();

        $client->request('GET', '/demo/pdo.php');

        // Wait for Debugbar to load
        $crawler = $client->waitFor('.phpdebugbar-body');
        usleep(1000);

        if (!$this->isTabActive($crawler, 'database')) {
            $client->click($this->getTabLink($crawler, 'database'));
        }

        $crawler = $client->waitForVisibility('.phpdebugbar-panel[data-collector=database]');

        $statements = $crawler->filter('.phpdebugbar-panel[data-collector=database] .phpdebugbar-widgets-sql')
            ->each(function($node){
                return $node->getText();
            });

        $this->assertEquals('insert into users (name) values (?)', $statements[1]);
        $this->assertCount(7, $statements);
    }

}