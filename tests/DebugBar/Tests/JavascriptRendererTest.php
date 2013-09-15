<?php

namespace DebugBar\Tests;

use DebugBar\JavascriptRenderer;
use DebugBar\DebugBar;
use DebugBar\StandardDebugBar;

class JavascriptRendererTest extends DebugBarTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->r = new JavascriptRenderer($this->debugbar);
    }

    public function testOptions()
    {
        $this->r->setOptions(array(
            'base_path' => '/foo',
            'base_url' => '/foo',
            'include_vendors' => false,
            'javascript_class' => 'Foobar',
            'variable_name' => 'foovar',
            'initialization' => JavascriptRenderer::INITIALIZE_CONTROLS,
            'controls' => array(
                'memory' => array(
                    "icon" => "cogs",
                    "map" => "memory.peak_usage_str",
                    "default" => "'0B'"
                )
            ),
            'disable_controls' => array('messages'),
            'ignore_collectors' => 'config'
        ));

        $this->assertEquals('/foo', $this->r->getBasePath());
        $this->assertEquals('/foo', $this->r->getBaseUrl());
        $this->assertFalse($this->r->areVendorsIncluded());
        $this->assertEquals('Foobar', $this->r->getJavascriptClass());
        $this->assertEquals('foovar', $this->r->getVariableName());
        $this->assertEquals(JavascriptRenderer::INITIALIZE_CONTROLS, $this->r->getInitialization());
        $controls = $this->r->getControls();
        $this->assertCount(2, $controls);
        $this->assertArrayHasKey('memory', $controls);
        $this->assertArrayHasKey('messages', $controls);
        $this->assertNull($controls['messages']);
        $this->assertContains('config', $this->r->getIgnoredCollectors());
    }

    public function testGetAssets()
    {
        $this->r->setBasePath('/foo');
        list($css, $js) = $this->r->getAssets();
        $this->assertContains('/foo/debugbar.css', $css);
        $this->assertContains('/foo/widgets.js', $js);
        $this->assertContains('/foo/vendor/jquery-1.8.3.min.js', $js);

        $this->r->setIncludeVendors(false);
        $js = $this->r->getAssets('js');
        $this->assertContains('/foo/debugbar.js', $js);
        $this->assertNotContains('/foo/vendor/jquery-1.8.3.min.js', $js);
    }

    public function testRenderHead()
    {
        $this->r->setBaseUrl('/foo');
        $html = $this->r->renderHead();
        $this->assertTag(array('tag' => 'script', 'attributes' => array('src' => '/foo/debugbar.js')), $html);
    }

    public function testRenderFullInitialization()
    {
        $this->debugbar->addCollector(new \DebugBar\DataCollector\MessagesCollector());
        $this->r->addControl('time', array('icon' => 'time', 'map' => 'time', 'default' => '"0s"'));
        $expected = file_get_contents(__DIR__ . '/full_init.html');
        $this->assertStringStartsWith($expected, $this->r->render());
    }

    public function testRenderConstructorOnly()
    {
        $this->r->setInitialization(JavascriptRenderer::INITIALIZE_CONSTRUCTOR);
        $this->r->setJavascriptClass('Foobar');
        $this->r->setVariableName('foovar');
        $this->r->setAjaxHandlerClass(false);
        $this->assertStringStartsWith("<script type=\"text/javascript\">\nvar foovar = new Foobar();\nfoovar.addDataSet(", $this->r->render());
    }
}