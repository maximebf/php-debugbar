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

use Twig_Environment;
use Twig_LexerInterface;
use Twig_ParserInterface;
use Twig_TokenStream;
use Twig_CompilerInterface;
use Twig_NodeInterface;
use Twig_LoaderInterface;
use Twig_ExtensionInterface;
use Twig_TokenParserInterface;
use Twig_NodeVisitorInterface;
use DebugBar\DataCollector\TimeDataCollector;

/**
 * Wrapped a Twig Environment to provide profiling features
 */
class TraceableTwigEnvironment extends Twig_Environment
{
    protected $twig;

    protected $renderedTemplates = array();

    protected $timeDataCollector;

    /**
     * @param Twig_Environment $twig
     * @param TimeDataCollector $timeDataCollector
     */
    public function __construct(Twig_Environment $twig, TimeDataCollector $timeDataCollector = null)
    {
        $this->twig = $twig;
        $this->timeDataCollector = $timeDataCollector;
    }

    public function getRenderedTemplates()
    {
        return $this->renderedTemplates;
    }

    public function addRenderedTemplate(array $info)
    {
        $this->renderedTemplates[] = $info;
    }

    public function getTimeDataCollector()
    {
        return $this->timeDataCollector;
    }

    /**
     * {@inheritDoc}
     */
    public function getBaseTemplateClass()
    {
        return $this->twig->getBaseTemplateClass();
    }

    /**
     * {@inheritDoc}
     */
    public function setBaseTemplateClass($class)
    {
        $this->twig->setBaseTemplateClass($class);
    }

    /**
     * {@inheritDoc}
     */
    public function enableDebug()
    {
        $this->twig->enableDebug();
    }

    /**
     * {@inheritDoc}
     */
    public function disableDebug()
    {
        $this->twig->disableDebug();
    }

    /**
     * {@inheritDoc}
     */
    public function isDebug()
    {
        return $this->twig->isDebug();
    }

    /**
     * {@inheritDoc}
     */
    public function enableAutoReload()
    {
        $this->twig->enableAutoReload();
    }

    /**
     * {@inheritDoc}
     */
    public function disableAutoReload()
    {
        $this->twig->disableAutoReload();
    }

    /**
     * {@inheritDoc}
     */
    public function isAutoReload()
    {
        return $this->twig->isAutoReload();
    }

    /**
     * {@inheritDoc}
     */
    public function enableStrictVariables()
    {
        $this->twig->enableStrictVariables();
    }

    /**
     * {@inheritDoc}
     */
    public function disableStrictVariables()
    {
        $this->twig->disableStrictVariables();
    }

    /**
     * {@inheritDoc}
     */
    public function isStrictVariables()
    {
        return $this->twig->isStrictVariables();
    }

    /**
     * {@inheritDoc}
     */
    public function getCache()
    {
        return $this->twig->getCache();
    }

    /**
     * {@inheritDoc}
     */
    public function setCache($cache)
    {
        $this->twig->setCache($cache);
    }

    /**
     * {@inheritDoc}
     */
    public function getCacheFilename($name)
    {
        return $this->twig->getCacheFilename($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateClass($name, $index = null)
    {
        return $this->twig->getTemplateClass($name, $index);
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateClassPrefix()
    {
        return $this->twig->getTemplateClassPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function render($name, array $context = array())
    {
        return $this->loadTemplate($name)->render($context);
    }

    /**
     * {@inheritDoc}
     */
    public function display($name, array $context = array())
    {
        $this->loadTemplate($name)->display($context);
    }

    /**
     * {@inheritDoc}
     */
    public function loadTemplate($name, $index = null)
    {
        $cls = $this->twig->getTemplateClass($name, $index);

        if (isset($this->twig->loadedTemplates[$cls])) {
            return $this->twig->loadedTemplates[$cls];
        }

        if (!class_exists($cls, false)) {
            if (false === $cache = $this->getCacheFilename($name)) {
                eval('?>'.$this->compileSource($this->getLoader()->getSource($name), $name));
            } else {
                if (!is_file($cache) || ($this->isAutoReload() && !$this->isTemplateFresh($name, filemtime($cache)))) {
                    $this->writeCacheFile($cache, $this->compileSource($this->getLoader()->getSource($name), $name));
                }

                require_once $cache;
            }
        }

        if (!$this->twig->runtimeInitialized) {
            $this->initRuntime();
        }

        return $this->twig->loadedTemplates[$cls] = new TraceableTwigTemplate($this, new $cls($this));
    }

    /**
     * {@inheritDoc}
     */
    public function isTemplateFresh($name, $time)
    {
        return $this->twig->isTemplateFresh($name, $time);
    }

    /**
     * {@inheritDoc}
     */
    public function resolveTemplate($names)
    {
        return $this->twig->resolveTemplate($names);
    }

    /**
     * {@inheritDoc}
     */
    public function clearTemplateCache()
    {
        $this->twig->clearTemplateCache();
    }

    /**
     * {@inheritDoc}
     */
    public function clearCacheFiles()
    {
        $this->twig->clearCacheFiles();
    }

    /**
     * {@inheritDoc}
     */
    public function getLexer()
    {
        return $this->twig->getLexer();
    }

    /**
     * {@inheritDoc}
     */
    public function setLexer(Twig_LexerInterface $lexer)
    {
        $this->twig->setLexer($lexer);
    }

    /**
     * {@inheritDoc}
     */
    public function tokenize($source, $name = null)
    {
        return $this->twig->tokenize($source, $name);
    }

    /**
     * {@inheritDoc}
     */
    public function getParser()
    {
        return $this->twig->getParser();
    }

    /**
     * {@inheritDoc}
     */
    public function setParser(Twig_ParserInterface $parser)
    {
        $this->twig->setParser($parser);
    }

    /**
     * {@inheritDoc}
     */
    public function parse(Twig_TokenStream $tokens)
    {
        return $this->twig->parse($tokens);
    }

    /**
     * {@inheritDoc}
     */
    public function getCompiler()
    {
        return $this->twig->getCompiler();
    }

    /**
     * {@inheritDoc}
     */
    public function setCompiler(Twig_CompilerInterface $compiler)
    {
        $this->twig->setCompiler($compiler);
    }

    /**
     * {@inheritDoc}
     */
    public function compile(Twig_NodeInterface $node)
    {
        return $this->twig->compile($node);
    }

    /**
     * {@inheritDoc}
     */
    public function compileSource($source, $name = null)
    {
        return $this->twig->compileSource($source, $name);
    }

    /**
     * {@inheritDoc}
     */
    public function setLoader(Twig_LoaderInterface $loader)
    {
        $this->twig->setLoader($loader);
    }

    /**
     * {@inheritDoc}
     */
    public function getLoader()
    {
        return $this->twig->getLoader();
    }

    /**
     * {@inheritDoc}
     */
    public function setCharset($charset)
    {
        $this->twig->setCharset($charset);
    }

    /**
     * {@inheritDoc}
     */
    public function getCharset()
    {
        return $this->twig->getCharset();
    }

    /**
     * {@inheritDoc}
     */
    public function initRuntime()
    {
        $this->twig->initRuntime();
    }

    /**
     * {@inheritDoc}
     */
    public function hasExtension($name)
    {
        return $this->twig->hasExtension($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getExtension($name)
    {
        return $this->twig->getExtension($name);
    }

    /**
     * {@inheritDoc}
     */
    public function addExtension(Twig_ExtensionInterface $extension)
    {
        $this->twig->addExtension($extension);
    }

    /**
     * {@inheritDoc}
     */
    public function removeExtension($name)
    {
        $this->twig->removeExtension($name);
    }

    /**
     * {@inheritDoc}
     */
    public function setExtensions(array $extensions)
    {
        $this->twig->setExtensions($extensions);
    }

    /**
     * {@inheritDoc}
     */
    public function getExtensions()
    {
        return $this->twig->getExtensions();
    }

    /**
     * {@inheritDoc}
     */
    public function addTokenParser(Twig_TokenParserInterface $parser)
    {
        $this->twig->addTokenParser($parser);
    }

    /**
     * {@inheritDoc}
     */
    public function getTokenParsers()
    {
        return $this->twig->getTokenParsers();
    }

    /**
     * {@inheritDoc}
     */
    public function getTags()
    {
        return $this->twig->getTags();
    }

    /**
     * {@inheritDoc}
     */
    public function addNodeVisitor(Twig_NodeVisitorInterface $visitor)
    {
        $this->twig->addNodeVisitor($visitor);
    }

    /**
     * {@inheritDoc}
     */
    public function getNodeVisitors()
    {
        return $this->twig->getNodeVisitors();
    }

    /**
     * {@inheritDoc}
     */
    public function addFilter($name, $filter = null)
    {
        $this->twig->addFilter($name, $filter);
    }

    /**
     * {@inheritDoc}
     */
    public function getFilter($name)
    {
        return $this->twig->getFilter($name);
    }

    /**
     * {@inheritDoc}
     */
    public function registerUndefinedFilterCallback($callable)
    {
        $this->twig->registerUndefinedFilterCallback($callable);
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return $this->twig->getFilters();
    }

    /**
     * {@inheritDoc}
     */
    public function addTest($name, $test = null)
    {
        $this->twig->addTest($name, $test);
    }

    /**
     * {@inheritDoc}
     */
    public function getTests()
    {
        return $this->twig->getTests();
    }

    /**
     * {@inheritDoc}
     */
    public function getTest($name)
    {
        return $this->twig->getTest($name);
    }

    /**
     * {@inheritDoc}
     */
    public function addFunction($name, $function = null)
    {
        $this->twig->addFunction($name, $function);
    }

    /**
     * {@inheritDoc}
     */
    public function getFunction($name)
    {
        return $this->twig->getFunction($name);
    }

    /**
     * {@inheritDoc}
     */
    public function registerUndefinedFunctionCallback($callable)
    {
        $this->twig->registerUndefinedFunctionCallback($callable);
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return $this->twig->getFunctions();
    }

    /**
     * {@inheritDoc}
     */
    public function addGlobal($name, $value)
    {
        $this->twig->addGlobal($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getGlobals()
    {
        return $this->twig->getGlobals();
    }

    /**
     * {@inheritDoc}
     */
    public function mergeGlobals(array $context)
    {
        return $this->twig->mergeGlobals($context);
    }

    /**
     * {@inheritDoc}
     */
    public function getUnaryOperators()
    {
        return $this->twig->getUnaryOperators();
    }

    /**
     * {@inheritDoc}
     */
    public function getBinaryOperators()
    {
        return $this->twig->getBinaryOperators();
    }

    /**
     * {@inheritDoc}
     */
    public function computeAlternatives($name, $items)
    {
        return $this->twig->computeAlternatives($name, $items);
    }
}
