<?php
/*
 * This file is part of the DebugBar package.
 *
 * (c) 2013 Maxime Bouroumeau-Fuseau
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DebugBar\Bridge\SwiftMailer;

use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;
use Swift_Mailer;
use Swift_Plugins_MessageLogger;

/**
 * Collects data abotu sent mails
 */
class MessagesCollector extends DataCollector implements Renderable
{
    protected $messagesLogger;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->messagesLogger = new Swift_Plugins_MessageLogger();
        $mailer->registerPlugin($this->messagesLogger);
    }

    /**
     * {@inheritDoc}
     */
    public function collect()
    {
        $mails = array();
        foreach ($this->messagesLogger->getMessages() as $msg) {
            $mails[] = array(
                'to' => $this->formatTo($msg->getTo()),
                'subject' => $msg->getSubject(),
                'headers' => $msg->getHeaders()->toString()
            );
        }
        return array(
            'count' => count($mails),
            'mails' => $mails
        );
    }

    protected function formatTo($to)
    {
        $f = array();
        foreach ($to as $k => $v) {
            $f[] = (empty($v) ? '' : "$v ") . "<$k>";
        }
        return implode(', ', $f);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'swiftmailer_messages';
    }

    /**
     * {@inheritDoc}
     */
    public function getWidgets()
    {
        return array(
            'swiftmailer_messages' => array(
                'widget' => 'PhpDebugBar.Widgets.MailsWidget',
                'map' => 'swiftmailer_messages.mails',
                'default' => '[]',
                'title' => 'Mails'
            ),
            'swiftmailer_messages:badge' => array(
                'map' => 'swiftmailer_messages.count',
                'default' => 0
            )
        );
    }
}
