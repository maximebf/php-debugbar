<?php

include 'bootstrap.php';

use DebugBar\DataCollector\PDO\TraceablePDO;
use DebugBar\DataCollector\PDO\PDOCollector;

$pdo = new TraceablePDO(new PDO('sqlite::memory:'));
$debugbar->addCollector(new PDOCollector($pdo));

$pdo->exec('create table users (name varchar)');
$stmt = $pdo->prepare('insert into users (name) values (?)');
$stmt->execute(array('foo'));
$stmt->execute(array('bar'));

$users = $pdo->query('select * from users')->fetchAll();
$stmt = $pdo->prepare('select * from users where name=?');
$stmt->execute(array('foo'));
$foo = $stmt->fetch();

$pdo->exec('delete from titi');

render_demo_page();
