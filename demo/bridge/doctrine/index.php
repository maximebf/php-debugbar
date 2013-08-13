<?php

include __DIR__ . '/bootstrap.php';
include __DIR__ . '/../../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../../src/DebugBar/Resources');

$debugStack = new Doctrine\DBAL\Logging\DebugStack();
$entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);
$debugbar->addCollector(new DebugBar\Bridge\DoctrineCollector($debugStack));

$product = new Product();
$product->setName("foobar");

$entityManager->persist($product);
$entityManager->flush();

render_demo_page();