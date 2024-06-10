<?php

include __DIR__ . '/bootstrap.php';
include __DIR__ . '/../../bootstrap.php';

$debugbarRenderer->setBaseUrl('../../../src/DebugBar/Resources');

$debugStack = new Doctrine\DBAL\Logging\DebugStack();
$entityManager->getConnection()->getConfiguration()->setSQLLogger($debugStack);
$debugbar->addCollector(new DebugBar\Bridge\DoctrineCollector($debugStack));

$product = new Demo\Product();
$product->setName("foobar");


$entityManager->persist($product);
$entityManager->flush();
$entityManager->createQuery("select p from  Demo\\Product p where p.name=:c")->setParameter("c", "<script>alert();</script>")->execute();
render_demo_page();
