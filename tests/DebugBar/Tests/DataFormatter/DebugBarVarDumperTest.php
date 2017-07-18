<?php

namespace DebugBar\Tests\DataFormatter;

use DebugBar\DataFormatter\DebugBarVarDumper;
use DebugBar\Tests\DebugBarTestCase;

class DebugBarVarDumperTest extends DebugBarTestCase
{
    const STYLE_STRING = 'SpecialStyleString';

    private $testStyles = array(
        'default' => self::STYLE_STRING,
    );

    public function testBasicFunctionality()
    {
        // Test that we can render a simple variable without dump headers
        $d = new DebugBarVarDumper();
        $d->mergeDumperOptions(array('styles' => $this->testStyles));
        $out = $d->renderVar('magic');

        $this->assertContains('magic', $out);
        $this->assertNotContains(self::STYLE_STRING, $out); // make sure there's no dump header

        // Test that we can capture a variable without rendering into a Data-type variable
        $data = $d->captureVar('hello');
        $this->assertContains('hello', $data);
        $deserialized = unserialize($data);
        $this->assertInstanceOf('Symfony\Component\VarDumper\Cloner\Data', $deserialized);

        // Test that we can render the captured variable at a later time
        $out = $d->renderCapturedVar($data);
        $this->assertContains('hello', $out);
        $this->assertNotContains(self::STYLE_STRING, $out); // make sure there's no dump header
    }

    public function testSeeking()
    {
        $testData = array(
            'one',
            array('two'),
            'three',
        );
        $d = new DebugBarVarDumper();
        $data = $d->captureVar($testData);

        // seek depth of 1
        $out = $d->renderCapturedVar($data, array(1));
        $this->assertNotContains('one', $out);
        $this->assertContains('array', $out);
        $this->assertContains('two', $out);
        $this->assertNotContains('three', $out);

        // seek depth of 2
        $out = $d->renderCapturedVar($data, array(1, 0));
        $this->assertNotContains('one', $out);
        $this->assertNotContains('array', $out);
        $this->assertContains('two', $out);
        $this->assertNotContains('three', $out);
    }

    public function testAssetProvider()
    {
        $d = new DebugBarVarDumper();
        $d->mergeDumperOptions(array('styles' => $this->testStyles));
        $assets = $d->getAssets();
        $this->assertArrayHasKey('inline_head', $assets);
        $this->assertCount(1, $assets);

        $inlineHead = $assets['inline_head'];
        $this->assertArrayHasKey('html_var_dumper', $inlineHead);
        $this->assertCount(1, $inlineHead);

        $assetText = $inlineHead['html_var_dumper'];
        $this->assertContains(self::STYLE_STRING, $assetText);
    }

    public function testBasicOptionOperations()
    {
        // Test basic get/merge/reset functionality for cloner
        $d = new DebugBarVarDumper();
        $options = $d->getClonerOptions();
        $this->assertEmpty($options);

        $d->mergeClonerOptions(array(
            'max_items' => 5,
        ));
        $d->mergeClonerOptions(array(
            'max_string' => 4,
        ));
        $d->mergeClonerOptions(array(
            'max_items' => 3,
        ));
        $options = $d->getClonerOptions();
        $this->assertEquals(array(
            'max_items' => 3,
            'max_string' => 4,
        ), $options);

        $d->resetClonerOptions(array(
            'min_depth' => 2,
        ));
        $options = $d->getClonerOptions();
        $this->assertEquals(array(
            'min_depth' => 2,
        ), $options);

        // Test basic get/merge/reset functionality for dumper
        $options = $d->getDumperOptions();
        $this->assertContains('styles', $options);
        $this->assertArrayHasKey('const', $options['styles']);
        $this->assertContains('expanded_depth', $options);
        $this->assertEquals(0, $options['expanded_depth']);
        $this->assertCount(2, $options);

        $d->mergeDumperOptions(array(
            'styles' => $this->testStyles,
        ));
        $d->mergeDumperOptions(array(
            'max_string' => 7,
        ));
        $options = $d->getDumperOptions();
        $this->assertEquals(array(
            'max_string' => 7,
            'styles' => $this->testStyles,
            'expanded_depth' => 0,
        ), $options);

        $d->resetDumperOptions(array(
            'styles' => $this->testStyles,
        ));
        $options = $d->getDumperOptions();
        $this->assertEquals(array(
            'styles' => $this->testStyles,
            'expanded_depth' => 0,
        ), $options);
    }

    public function testClonerOptions()
    {
        // Test the actual operation of the cloner options
        $d = new DebugBarVarDumper();

        // Test that the 'casters' option can remove default casters
        $testData = function() {};
        $d->resetClonerOptions();
        $this->assertContains('DebugBarVarDumperTest.php', $d->renderVar($testData));

        $d->resetClonerOptions(array(
            'casters' => array(),
        ));
        $this->assertNotContains('DebugBarVarDumperTest.php', $d->renderVar($testData));

        // Test that the 'additional_casters' option can add new casters
        $testData = function() {};
        $d->resetClonerOptions();
        $this->assertContains('DebugBarVarDumperTest.php', $d->renderVar($testData));

        $d->resetClonerOptions(array(
            'casters' => array(),
            'additional_casters' => array('Closure' => 'Symfony\Component\VarDumper\Caster\ReflectionCaster::castClosure'),
        ));
        $this->assertContains('DebugBarVarDumperTest.php', $d->renderVar($testData));

        // Test 'max_items'
        $testData = array(array('one', 'two', 'three', 'four', 'five'));
        $d->resetClonerOptions();
        $out = $d->renderVar($testData);
        foreach ($testData[0] as $search) {
            $this->assertContains($search, $out);
        }

        $d->resetClonerOptions(array(
            'max_items' => 3,
        ));
        $out = $d->renderVar($testData);
        $this->assertContains('one', $out);
        $this->assertContains('two', $out);
        $this->assertContains('three', $out);
        $this->assertNotContains('four', $out);
        $this->assertNotContains('five', $out);

        // Test 'max_string'
        $testData = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $d->resetClonerOptions();
        $this->assertContains($testData, $d->renderVar($testData));

        $d->resetClonerOptions(array(
            'max_string' => 10,
        ));
        $out = $d->renderVar($testData);
        $this->assertContains('ABCDEFGHIJ', $out);
        $this->assertNotContains('ABCDEFGHIJK', $out);

        // Test 'min_depth' if we are on a Symfony version that supports it
        if (method_exists('Symfony\Component\VarDumper\Cloner\AbstractCloner', 'setMinDepth')) {
            $testData = array('one', 'two', 'three', 'four', 'five');
            $d->resetClonerOptions(array(
                'max_items' => 3,
            ));
            $out = $d->renderVar($testData);
            foreach ($testData as $search) {
                $this->assertContains($search, $out);
            }

            $d->resetClonerOptions(array(
                'min_depth' => 0,
                'max_items' => 3,
            ));
            $out = $d->renderVar($testData);
            $this->assertContains('one', $out);
            $this->assertContains('two', $out);
            $this->assertContains('three', $out);
            $this->assertNotContains('four', $out);
            $this->assertNotContains('five', $out);
        }
    }

    public function testDumperOptions()
    {
        // Test the actual operation of the dumper options
        $d = new DebugBarVarDumper();

        // Test that the 'styles' option affects assets
        $d->resetDumperOptions();
        $assets = $d->getAssets();
        $this->assertNotContains(self::STYLE_STRING, $assets['inline_head']['html_var_dumper']);

        $d->resetDumperOptions(array('styles' => $this->testStyles));
        $assets = $d->getAssets();
        $this->assertContains(self::STYLE_STRING, $assets['inline_head']['html_var_dumper']);

        // The next tests require changes in Symfony 3.2:
        $dumpMethod = new \ReflectionMethod('Symfony\Component\VarDumper\Dumper\HtmlDumper', 'dump');
        if ($dumpMethod->getNumberOfParameters() >= 3) {
            // Test that the 'expanded_depth' option affects output
            $d->resetDumperOptions(array('expanded_depth' => 123321));
            $out = $d->renderVar(true);
            $this->assertContains('123321', $out);

            // Test that the 'max_string' option affects output
            $d->resetDumperOptions(array('max_string' => 321123));
            $out = $d->renderVar(true);
            $this->assertContains('321123', $out);

            // Test that the 'file_link_format' option affects output
            $d->resetDumperOptions(array('file_link_format' => 'fmt%ftest'));
            $out = $d->renderVar(function() {});
            $this->assertContains('DebugBarVarDumperTest.phptest', $out);
        }
    }
}
