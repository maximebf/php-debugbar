<?php

namespace DebugBar\Tests;

use DebugBar\JavascriptRenderer;
use DebugBar\DebugBar;
use DebugBar\StandardDebugBar;
use DebugBar\Widget\Indicator;

class JavascriptRendererTest extends DebugBarTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->r = new JavascriptRenderer($this->debugbar);
        $this->r->setBasePath('/bpath');
        $this->r->setBaseUrl('/burl');
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
            'widgets' => array(
                'memory' => new Indicator('cogs', 'memory', '"OB"')
            ),
            'disable_widgets' => array('messages'),
            'ignore_collectors' => 'config',
            'ajax_handler_classname' => 'AjaxFoo',
            'ajax_handler_bind_to_jquery' => false,
            'open_handler_classname' => 'OpenFoo',
            'server_handler_url' => 'server.php',
            'ctor_options' => array('foo' => 'bar')
        ));

        $this->assertEquals('/foo', $this->r->getBasePath());
        $this->assertEquals('/foo', $this->r->getBaseUrl());
        $this->assertFalse($this->r->areVendorsIncluded());
        $this->assertEquals('Foobar', $this->r->getJavascriptClass());
        $this->assertEquals('foovar', $this->r->getVariableName());
        $this->assertEquals(JavascriptRenderer::INITIALIZE_CONTROLS, $this->r->getInitialization());
        $this->assertTrue($this->r->isJqueryNoConflictEnabled());
        $widgets = $this->r->getWidgets();
        $this->assertCount(2, $widgets);
        $this->assertArrayHasKey('memory', $widgets);
        $this->assertArrayHasKey('messages', $widgets);
        $this->assertNull($widgets['messages']);
        $this->assertContains('config', $this->r->getIgnoredCollectors());
        $this->assertEquals('AjaxFoo', $this->r->getAjaxHandlerClass());
        $this->assertFalse($this->r->isAjaxHandlerBoundToJquery());
        $this->assertEquals('OpenFoo', $this->r->getOpenHandlerClass());
        $this->assertEquals('server.php', $this->r->getServerHandlerUrl());
        $this->assertArrayHasKey('foo', $this->r->getConstructorOptions());
    }

    public function testAddAssets()
    {
        $this->r->addAssets('foo.css', 'foo.js', '/bar', '/foobar');

        list($css, $js) = $this->r->getAssets();
        $this->assertContains('/bar/foo.css', $css);
        $this->assertContains('/bar/foo.js', $js);

        $html = $this->r->renderHead();
        $this->assertTag(array('tag' => 'script', 'attributes' => array('src' => '/foobar/foo.js')), $html);
    }

    public function testGetAssets()
    {
        list($css, $js) = $this->r->getAssets();
        $this->assertContains('/bpath/debugbar.css', $css);
        $this->assertContains('/bpath/widgets.js', $js);
        $this->assertContains('/bpath/vendor/jquery/dist/jquery.min.js', $js);

        $this->r->setIncludeVendors(false);
        $js = $this->r->getAssets('js');
        $this->assertContains('/bpath/debugbar.js', $js);
        $this->assertNotContains('/bpath/vendor/jquery/dist/jquery.min.js', $js);
    }

    public function testRenderHead()
    {
        $html = $this->r->renderHead();
        $this->assertTag(array('tag' => 'script', 'attributes' => array('src' => '/burl/debugbar.js')), $html);
    }

    public function testRenderFullInitialization()
    {
        $this->debugbar->addCollector(new \DebugBar\DataCollector\MessagesCollector());
        $this->r->addWidget('time', new Indicator('time', 'time', '"0s"'));
        $expected = rtrim(file_get_contents(__DIR__ . '/full_init.html'));
        $this->assertStringStartsWith($expected, $this->r->render());
    }

    public function testRenderConstructorOnly()
    {
        $this->r->setInitialization(JavascriptRenderer::INITIALIZE_CONSTRUCTOR);
        $this->r->setJavascriptClass('Foobar');
        $this->r->setVariableName('foovar');
        $this->r->setAjaxHandlerClass(false);
        $this->r->setEnableJqueryNoConflict(true);
        $this->assertStringStartsWith("<script type=\"text/javascript\">\njQuery.noConflict(true);\nvar foovar = new Foobar({});\nfoovar.addDataSet(", $this->r->render());
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
