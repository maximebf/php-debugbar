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
            'enable_jquery_noconflict' => true,
            'controls' => array(
                'memory' => array(
                    "icon" => "cogs",
                    "map" => "memory.peak_usage_str",
                    "default" => "'0B'"
                )
            ),
            'disable_controls' => array('messages'),
            'ignore_collectors' => 'config',
            'ajax_handler_classname' => 'AjaxFoo',
            'ajax_handler_bind_to_jquery' => false,
            'open_handler_classname' => 'OpenFoo',
            'open_handler_url' => 'open.php'
        ));

        $this->assertEquals('/foo', $this->r->getBasePath());
        $this->assertEquals('/foo', $this->r->getBaseUrl());
        $this->assertFalse($this->r->areVendorsIncluded());
        $this->assertEquals('Foobar', $this->r->getJavascriptClass());
        $this->assertEquals('foovar', $this->r->getVariableName());
        $this->assertEquals(JavascriptRenderer::INITIALIZE_CONTROLS, $this->r->getInitialization());
        $this->assertTrue($this->r->isJqueryNoConflictEnabled());
        $controls = $this->r->getControls();
        $this->assertCount(2, $controls);
        $this->assertArrayHasKey('memory', $controls);
        $this->assertArrayHasKey('messages', $controls);
        $this->assertNull($controls['messages']);
        $this->assertContains('config', $this->r->getIgnoredCollectors());
        $this->assertEquals('AjaxFoo', $this->r->getAjaxHandlerClass());
        $this->assertFalse($this->r->isAjaxHandlerBoundToJquery());
        $this->assertEquals('OpenFoo', $this->r->getOpenHandlerClass());
        $this->assertEquals('open.php', $this->r->getOpenHandlerUrl());
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
        $this->r->setEnableJqueryNoConflict(true);
        $this->assertStringStartsWith("<script type=\"text/javascript\">\njQuery.noConflict(true);\nvar foovar = new Foobar();\nfoovar.addDataSet(", $this->r->render());
    }

    public function testJQueryNoConflictAutoDisabling()
    {
        $this->assertTrue($this->r->isJqueryNoConflictEnabled());
        $this->r->setIncludeVendors(false);
        $this->assertFalse($this->r->isJqueryNoConflictEnabled());
        $this->r->setEnableJqueryNoConflict(true);
        $this->r->setIncludeVendors('css');
        $this->assertFalse($this->r->isJqueryNoConflictEnabled());
        $this->r->setEnableJqueryNoConflict(true);
        $this->r->setIncludeVendors(array('css', 'js'));
        $this->assertTrue($this->r->isJqueryNoConflictEnabled());
    }
}