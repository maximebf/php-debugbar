<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\ServerHandler;

use DebugBar\DebugBar;
use DebugBar\DebugBarException;

/**
 * Handles and dispatches requests from the debugbar to handlers
 */
class FrontController implements ServerHandlerInterface
{
    protected $debugBar;

    protected $handlers = array();

    public function __construct(DebugBar $debugbar)
    {
        $this->debugBar = $debugbar;
        $this->registerHandler($this);

        if ($debugbar->isDataPersisted()) {
            $this->registerHandler(new OpenHandler());
        }

        foreach ($debugbar->getCollectors() as $collector) {
            if ($collector instanceof ServerHandlerInterface) {
                $this->registerHandler($collector);
            } else if ($collector instanceof ServerHandlerFactoryInterface) {
                $this->registerHandler($collector->getServerHandler());
            }
        }
    }

    /**
     * Registers a new ServerHandlerInterface
     *
     * @param ServerHandlerInterface $handler
     */
    public function registerHandler(ServerHandlerInterface $handler)
    {
        $this->handlers[$handler->getName()] = $handler;
        return $this;
    }

    /**
     * Checks if an handler is registered by name
     *
     * @param string $name
     * @return boolean
     */
    public function isHandlerRegistered($name)
    {
        return isset($this->handlers[$name]);
    }

    /**
     * Returns all handlers
     *
     * @return array
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * Handles the current request
     *
     * @param array $request Request data
     */
    public function handle($request = null, $echo = true, $sendHeader = true)
    {
        if ($request === null) {
            $request = $_REQUEST;
        }

        if (!isset($request['hdl'])) {
            throw new DebugBarException("Missing handler name");
        }
        $hdl = $request['hdl'];
        unset($request['hdl']);
        if (!isset($this->handlers[$hdl])) {
            throw new DebugBarException("Not handlers named '{$hdl}'");
        }
        $handler = $this->handlers[$hdl];

        $op = null;
        if (isset($request['op'])) {
            $op = $request['op'];
            unset($request['op']);
            if (!in_array($op, $handler->getCommandNames()) || !method_exists($handler, $op)) {
                throw new DebugBarException("Invalid operation '{$op}'");
            }
        }

        if ($sendHeader) {
            $this->debugBar->getHttpDriver()->setHeaders(array(
                    'Content-Type'=> 'application/json'
                ));
        }

        $response = json_encode(call_user_func(array($handler, $op), $request, $this->debugBar));
        if ($echo) {
            echo $response;
        }
        return $response;
    }

    public function getName()
    {
        return 'debugbar';
    }

    public function getCommandNames()
    {
        return array('ping');
    }

    public function ping($debugbar, $request)
    {
        return 'pong';
    }
}