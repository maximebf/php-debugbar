<?php

namespace DebugBar\DataFormatter;

use DebugBar\DataCollector\AssetProvider;
use Symfony\Component\VarDumper\Caster\Caster;
use Symfony\Component\VarDumper\Cloner\Cursor;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\Cloner\DumperInterface;
use Symfony\Component\VarDumper\Cloner\Stub;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * Clones and renders variables in HTML format using the Symfony VarDumper component.
 *
 * Cloning is decoupled from rendering, so that dumper users can have the fastest possible cloning
 * performance, while delaying rendering until it is actually needed.
 */
class DebugBarVarDumper implements AssetProvider
{
    protected static $defaultClonerOptions = array();

    protected static $defaultDumperOptions = array(
        'expanded_depth' => 0,
        'styles' => array(
            // NOTE:  'default' CSS is also specified in debugbar.css
            'default' => 'word-wrap: break-word; white-space: pre-wrap; word-break: normal',
            'num' => 'font-weight:bold; color:#1299DA',
            'const' => 'font-weight:bold',
            'str' => 'font-weight:bold; color:#3A9B26',
            'note' => 'color:#1299DA',
            'ref' => 'color:#7B7B7B',
            'public' => 'color:#000000',
            'protected' => 'color:#000000',
            'private' => 'color:#000000',
            'meta' => 'color:#B729D9',
            'key' => 'color:#3A9B26',
            'index' => 'color:#1299DA',
            'ellipsis' => 'color:#A0A000',
        ),
    );

    protected $clonerOptions;

    protected $dumperOptions;

    /** @var VarCloner */
    protected $cloner;

    /** @var DebugBarHtmlDumper */
    protected $dumper;

    /**
     * Gets the VarCloner instance with configuration options set.
     *
     * @return VarCloner
     */
    protected function getCloner()
    {
        if (!$this->cloner) {
            $clonerOptions = $this->getClonerOptions();
            if (isset($clonerOptions['casters'])) {
                $this->cloner = new VarCloner($clonerOptions['casters']);
            } else {
                $this->cloner = new VarCloner();
            }
            if (isset($clonerOptions['additional_casters'])) {
                $this->cloner->addCasters($clonerOptions['additional_casters']);
            }
            if (isset($clonerOptions['max_items'])) {
                $this->cloner->setMaxItems($clonerOptions['max_items']);
            }
            if (isset($clonerOptions['max_string'])) {
                $this->cloner->setMaxString($clonerOptions['max_string']);
            }
            // setMinDepth was added to Symfony 3.4:
            if (isset($clonerOptions['min_depth']) && method_exists($this->cloner, 'setMinDepth')) {
                $this->cloner->setMinDepth($clonerOptions['min_depth']);
            }
        }
        return $this->cloner;
    }

    /**
     * Gets the DebugBarHtmlDumper instance with configuration options set.
     *
     * @return DebugBarHtmlDumper
     */
    protected function getDumper()
    {
        if (!$this->dumper) {
            $this->dumper = new DebugBarHtmlDumper();
            $dumperOptions = $this->getDumperOptions();
            if (isset($dumperOptions['styles'])) {
                $this->dumper->setStyles($dumperOptions['styles']);
            }
        }
        return $this->dumper;
    }

    /**
     * Gets the array of non-default VarCloner configuration options.
     *
     * @return array
     */
    public function getClonerOptions()
    {
        if ($this->clonerOptions === null) {
            $this->clonerOptions = self::$defaultClonerOptions;
        }
        return $this->clonerOptions;
    }

    /**
     * Merges an array of non-default VarCloner configuration options with the existing non-default
     * options.
     *
     * Configuration options are:
     *  - casters: a map of VarDumper Caster objects to use instead of the default casters.
     *  - additional_casters: a map of VarDumper Caster objects to use in addition to the default
     *    casters.
     *  - max_items: maximum number of items to clone beyond the minimum depth.
     *  - max_string: maximum string size
     *  - min_depth: minimum tree depth to clone before counting items against the max_items limit.
     *    (Requires Symfony 3.4; ignored on older versions.)
     *
     * @param array $options
     */
    public function mergeClonerOptions($options)
    {
        $this->clonerOptions = $options + $this->getClonerOptions();
        $this->cloner = null;
    }

    /**
     * Resets the array of non-default VarCloner configuration options without retaining any of the
     * existing non-default options.
     *
     * Configuration options are:
     *  - casters: a map of VarDumper Caster objects to use instead of the default casters.
     *  - additional_casters: a map of VarDumper Caster objects to use in addition to the default
     *    casters.
     *  - max_items: maximum number of items to clone beyond the minimum depth.
     *  - max_string: maximum string size
     *  - min_depth: minimum tree depth to clone before counting items against the max_items limit.
     *    (Requires Symfony 3.4; ignored on older versions.)
     *
     * @param array $options
     */
    public function resetClonerOptions($options = null)
    {
        $this->clonerOptions = ($options ?: array()) + self::$defaultClonerOptions;
        $this->cloner = null;
    }

    /**
     * Gets the array of non-default HtmlDumper configuration options.
     *
     * @return array
     */
    public function getDumperOptions()
    {
        if ($this->dumperOptions === null) {
            $this->dumperOptions = self::$defaultDumperOptions;
        }
        return $this->dumperOptions;
    }

    /**
     * Merges an array of non-default HtmlDumper configuration options with the existing non-default
     * options.
     *
     * Configuration options are:
     *  - styles: a map of CSS styles to include in the assets, as documented in
     *    HtmlDumper::setStyles.
     *  - expanded_depth: the tree depth to initially expand.
     *    (Requires Symfony 3.2; ignored on older versions.)
     *  - max_string: maximum string size.
     *    (Requires Symfony 3.2; ignored on older versions.)
     *  - file_link_format: link format for files; %f expanded to file and %l expanded to line
     *    (Requires Symfony 3.2; ignored on older versions.)
     *
     * @param array $options
     */
    public function mergeDumperOptions($options)
    {
        $this->dumperOptions = $options + $this->getDumperOptions();
        $this->dumper = null;
    }

    /**
     * Resets the array of non-default HtmlDumper configuration options without retaining any of the
     * existing non-default options.
     *
     * Configuration options are:
     *  - styles: a map of CSS styles to include in the assets, as documented in
     *    HtmlDumper::setStyles.
     *  - expanded_depth: the tree depth to initially expand.
     *    (Requires Symfony 3.2; ignored on older versions.)
     *  - max_string: maximum string size.
     *    (Requires Symfony 3.2; ignored on older versions.)
     *  - file_link_format: link format for files; %f expanded to file and %l expanded to line
     *    (Requires Symfony 3.2; ignored on older versions.)
     *
     * @param array $options
     */
    public function resetDumperOptions($options = null)
    {
        $this->dumperOptions = ($options ?: array()) + self::$defaultDumperOptions;
        $this->dumper = null;
    }

    /**
     * Captures the data from a variable and serializes it for later rendering.
     *
     * @param mixed $data The variable to capture.
     * @return string Serialized variable data.
     */
    public function captureVar($data)
    {
        return serialize($this->getCloner()->cloneVar($data));
    }

    /**
     * Gets the display options for the HTML dumper.
     *
     * @return array
     */
    protected function getDisplayOptions()
    {
        $displayOptions = array();
        $dumperOptions = $this->getDumperOptions();
        // Only used by Symfony 3.2 and newer:
        if (isset($dumperOptions['expanded_depth'])) {
            $displayOptions['maxDepth'] = $dumperOptions['expanded_depth'];
        }
        // Only used by Symfony 3.2 and newer:
        if (isset($dumperOptions['max_string'])) {
            $displayOptions['maxStringLength'] = $dumperOptions['max_string'];
        }
        // Only used by Symfony 3.2 and newer:
        if (isset($dumperOptions['file_link_format'])) {
            $displayOptions['fileLinkFormat'] = $dumperOptions['file_link_format'];
        }
        return $displayOptions;
    }

    /**
     * Renders previously-captured data from captureVar to HTML and returns it as a string.
     *
     * @param string $capturedData Captured data from captureVar.
     * @param array $seekPath Pass an array of keys to traverse if you only want to render a subset
     *                        of the data.
     * @return string HTML rendering of the variable.
     */
    public function renderCapturedVar($capturedData, $seekPath = array())
    {
        $data = unserialize($capturedData);
        // The seek method was added in Symfony 3.2; emulate the behavior via SeekingData for older
        // Symfony versions.
        if (!method_exists($data, 'seek')) {
            $data = new SeekingData($data->getRawData());
        }

        foreach ($seekPath as $key) {
            $data = $data->seek($key);
        }

        return $this->dump($data);
    }

    /**
     * Captures and renders the data from a variable to HTML and returns it as a string.
     *
     * @param mixed $data The variable to capture and render.
     * @return string HTML rendering of the variable.
     */
    public function renderVar($data)
    {
        return $this->dump($this->getCloner()->cloneVar($data));
    }

    /**
     * Returns assets required for rendering variables.
     *
     * @return array
     */
    public function getAssets() {
        $dumper = $this->getDumper();
        $dumper->setDumpHeader(null); // this will cause the default dump header to regenerate
        return array(
            'inline_head' => array(
                'html_var_dumper' => $dumper->getDumpHeaderByDebugBar(),
            ),
        );
    }

    /**
     * Helper function to dump a Data object to HTML.
     *
     * @param Data $data
     * @return string
     */
    protected function dump(Data $data)
    {
        $dumper = $this->getDumper();
        $output = fopen('php://memory', 'r+b');
        $dumper->setOutput($output);
        $dumper->setDumpHeader(''); // we don't actually want a dump header
        // NOTE:  Symfony 3.2 added the third $extraDisplayOptions parameter.  Older versions will
        // safely ignore it.
        $dumper->dump($data, null, $this->getDisplayOptions());
        $result = stream_get_contents($output, -1, 0);
        fclose($output);
        return $result;
    }
}

/**
 * We have to extend the base HtmlDumper class in order to get access to the protected-only
 * getDumpHeader function.
 */
class DebugBarHtmlDumper extends HtmlDumper
{
    public function getDumpHeaderByDebugBar() {
        // getDumpHeader is protected:
        return $this->getDumpHeader();
    }
}

/**
 * This class backports the seek() function from Symfony 3.2 to older versions - up to v2.6.  The
 * class should not be used with newer Symfony versions that provide the seek function, as it relies
 * on a lot of undocumented functionality.
 */
class SeekingData extends Data
{
    // Because the class copies/pastes the seek() implementation from Symfony 3.2, we reproduce its
    // copyright here; this class is subject to the following additional copyright:

    /*
     * Copyright (c) 2014-2017 Fabien Potencier
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is furnished
     * to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in all
     * copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
     * THE SOFTWARE.
     */
    private $position = 0;
    private $key = 0;

    /**
     * Seeks to a specific key in nested data structures.
     *
     * @param string|int $key The key to seek to
     *
     * @return self|null A clone of $this of null if the key is not set
     */
    public function seek($key)
    {
        $thisData = $this->getRawData();
        $item = $thisData[$this->position][$this->key];

        if (!$item instanceof Stub || !$item->position) {
            return;
        }
        $keys = array($key);

        switch ($item->type) {
            case Stub::TYPE_OBJECT:
                $keys[] = "\0+\0".$key;
                $keys[] = "\0*\0".$key;
                $keys[] = "\0~\0".$key;
                $keys[] = "\0$item->class\0$key";
            case Stub::TYPE_ARRAY:
            case Stub::TYPE_RESOURCE:
                break;
            default:
                return;
        }

        $data = null;
        $children = $thisData[$item->position];

        foreach ($keys as $key) {
            if (isset($children[$key]) || array_key_exists($key, $children)) {
                $data = clone $this;
                $data->key = $key;
                $data->position = $item->position;
                break;
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function dump(DumperInterface $dumper)
    {
        // Override the base class dump to use the position and key
        $refs = array(0);
        $class = new \ReflectionClass($this);
        $dumpItem = $class->getMethod('dumpItem');
        $dumpItem->setAccessible(true);
        $data = $this->getRawData();
        $args = array($dumper, new Cursor(), &$refs, $data[$this->position][$this->key]);
        $dumpItem->invokeArgs($this, $args);
    }
}
