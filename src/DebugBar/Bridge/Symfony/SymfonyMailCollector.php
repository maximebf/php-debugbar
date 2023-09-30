<?php

namespace DebugBar\Bridge\Symfony;

use DebugBar\DataCollector\AssetProvider;
use DebugBar\DataCollector\DataCollector;
use DebugBar\DataCollector\Renderable;

/**
 * Collects data about sent mail events
 *
 * https://github.com/symfony/mailer
 */
class SymfonyMailCollector extends DataCollector implements Renderable, AssetProvider
{
    /** @var array */
    private $messages = [];

    /** @var bool */
    private $showDetailed = false;

    /** @param \Symfony\Component\Mailer\SentMessage $message */
    public function addSymfonyMessage($message)
    {
        $this->messages[] = $message->getOriginalMessage();
    }

    public function showMessageDetail()
    {
        $this->showDetailed = true;
    }

    public function collect()
    {
        $mails = [];

        foreach ($this->messages as $message) {
            /* @var \Symfony\Component\Mime\Message $message */
            $mails[] = [
                'to' => array_map(function ($address) {
                    /* @var \Symfony\Component\Mime\Address $address */
                    return $address->toString();
                }, $message->getTo()),
                'subject' => $message->getSubject(),
                'headers' => ($this->showDetailed ? $message : $message->getHeaders())->toString(),
            ];
        }

        return [
            'count' => count($mails),
            'mails' => $mails,
        ];
    }

    public function getName()
    {
        return 'symfonymailer_mails';
    }

    public function getWidgets()
    {
        return [
            'emails' => [
                'icon' => 'inbox',
                'widget' => 'PhpDebugBar.Widgets.MailsWidget',
                'map' => 'symfonymailer_mails.mails',
                'default' => '[]',
                'title' => 'Mails'
            ],
            'emails:badge' => [
                'map' => 'symfonymailer_mails.count',
                'default' => 'null'
            ]
        ];
    }

    public function getAssets()
    {
        return [
            'css' => 'widgets/mails/widget.css',
            'js' => 'widgets/mails/widget.js'
        ];
    }
}
