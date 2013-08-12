<?php

include __DIR__ . '/vendor/autoload.php';
include __DIR__ . '/../../bootstrap.php';

use DebugBar\Bridge\SwiftMailer\LogCollector;
use DebugBar\Bridge\SwiftMailer\MessagesCollector;

$mailer = Swift_Mailer::newInstance(Swift_NullTransport::newInstance());

$debugbar['messages']->aggregate(new LogCollector($mailer));
$debugbar->addCollector(new MessagesCollector($mailer));
$debugbarRenderer->setBaseUrl('../../../src/DebugBar/Resources');

$message = Swift_Message::newInstance('Wonderful Subject')
  ->setFrom(array('john@doe.com' => 'John Doe'))
  ->setTo(array('receiver@domain.org', 'other@domain.org' => 'A name'))
  ->setBody('Here is the message itself');

$mailer->send($message);


render_demo_page();