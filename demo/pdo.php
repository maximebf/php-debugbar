<?php

include 'bootstrap.php';

use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\PDO\PDOCollector;

$pdo = new TraceablePDO(new PDO('sqlite::memory:'));
$debugbar->addCollector(new PDOCollector($pdo));

$pdo->exec('create table users (name varchar)');
$stmt = $pdo->prepare('insert into users (name) values (?)');
$stmt->execute(['foo']);
$stmt->execute(['bar']);

$users = $pdo->query('select * from users')->fetchAll();
$stmt = $pdo->prepare('select * from users where name=?');
$stmt->execute(['foo']);
$foo = $stmt->fetch();

$pdo->exec('delete from users');

render_demo_page();
