<?php

namespace DebugBar\Tests\DataCollector\PDO;

use DebugBar\Tests\DebugBarTestCase;
use DebugBar\DataCollector\PDO\TraceablePDO;

class TraceablePDOTest extends DebugBarTestCase
{
    public function testExec()
    {
        $pdo = new TraceablePDO(new \PDO('sqlite::memory:'));
        $pdo->exec('CREATE TABLE a ( id INT NOT NULL )');
        $this->assertEquals(1, count($pdo->getExecutedStatements()));
    }

    public function testCanCallSubclassMethods()
    {
        $pdo = new TraceablePDO(new PDFOO('sqlite::memory:'));
        $this->assertSame('foo', $pdo->foo());
    }
}
