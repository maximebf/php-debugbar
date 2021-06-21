<?php

namespace DebugBar\Tests\DataCollector;

use DebugBar\DataFormatter\DataFormatter;
use DebugBar\Tests\DebugBarTestCase;

class Propel2CollectorTest extends DebugBarTestCase
{
    /**
     * @var null|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stub = null;
    /* @var null|DataFormatter */
    protected $dataFormatter = null;

    public function setUp(): void
    {
        $config = array(
            'slowTreshold' => 0.1,
            'details' => array(
                'time' => array(
                    'name' => 'Time',
                    'precision' => 3,
                    'pad' => 8,
                ),
                'mem' => array(
                    'name' => 'Memory',
                    'precision' => 3,
                    'pad' => 8,
                ),
                'memDelta' => array(
                    'name' => 'Memory Delta',
                    'precision' => 3,
                    'pad' => 8,
                ),
                'memPeak' => array(
                    'name' => 'Memory Peak',
                    'precision' => 3,
                    'pad' => 8,
                ),
            ),
            'innerGlue' => ': ',
            'outerGlue' => ' | ',
        );

        $stub = $this->getMockBuilder('DebugBar\Bridge\Propel2Collector')
            ->disableOriginalConstructor()
            ->setMethods(array('getDataFormatter', 'getHandler', 'getConfig'))
            ->getMock();

        $this->dataFormatter = new DataFormatter();

        $stub->method('getDataFormatter')->willReturn($this->dataFormatter);

        $stub->method('getConfig')->willReturn($config);

        $this->stub = $stub;
    }

    protected function equals($correctResult, $record)
    {
        $this->stub->method('getHandler')->willReturn(new MockHandler($record));
        $this->assertEquals($correctResult, $this->stub->collect());
    }

    public function testSimpleMessage()
    {
        $record = array(
            'message' => 'Simple message',
            'context' => 'propel',
            'level' => 200,
            'level_name' => 'INFO',
            'channel' => 'propel',
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
            'extra' => array(),
        );

        $correctResult = array(
            'nb_statements' => 0,
            'nb_failed_statements' => 0,
            'accumulated_duration' => 0,
            'accumulated_duration_str' => $this->dataFormatter->formatDuration(0),
            'memory_usage' => 0,
            'memory_usage_str' => $this->dataFormatter->formatBytes(0),
            'statements' =>  array(
                array(
                    'sql' => 'Simple message',
                    'is_success' => true,
                    'duration' => null,
                    'duration_str' => $this->dataFormatter->formatDuration(0),
                    'memory' => null,
                    'memory_str' => $this->dataFormatter->formatBytes(0),
                ),
            )
        );
        $this->equals($correctResult, array($record));
    }

    public function testErrorMessage()
    {
        $record = array(
            'message' => 'Error message',
            'context' => 'propel',
            'level' => 500,
            'level_name' => 'critical',
            'channel' => 'propel',
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
            'extra' => array(),
        );

        $correctResult = array(
            'nb_statements' => 0,
            'nb_failed_statements' => 1,
            'accumulated_duration' => 0,
            'accumulated_duration_str' => $this->dataFormatter->formatDuration(0),
            'memory_usage' => 0,
            'memory_usage_str' => $this->dataFormatter->formatBytes(0),
            'statements' =>  array(
                array(
                    'sql' => '',
                    'is_success' => false,
                    'error_code' => 500,
                    'error_message' => 'Error message',
                    'duration' => null,
                    'duration_str' => $this->dataFormatter->formatDuration(0),
                    'memory' => null,
                    'memory_str' => $this->dataFormatter->formatBytes(0),
                ),
            )
        );

        $this->equals($correctResult, array($record));
    }

    public function testProfileMessage()
    {
        $record = array(
            'message' => '     Time: 0.100ms | Memory:  1MB | Memory Delta: +1.0kB | Memory Peak:  2MB | SELECT id, first_name, last_name FROM author WHERE id = 1',
            'context' => 'propel',
            'level' => 200,
            'level_name' => 'info',
            'channel' => 'propel',
            'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
            'extra' => array(),
        );

        $correctResult = array(
            'nb_statements' => 1,
            'nb_failed_statements' => 0,
            'accumulated_duration' => 0.0001,
            'accumulated_duration_str' => $this->dataFormatter->formatDuration(0.0001),
            'memory_usage' => 1024.0,
            'memory_usage_str' => $this->dataFormatter->formatBytes(1024.0),
            'statements' =>  array(
                array(
                    'sql' => 'SELECT id, first_name, last_name FROM author WHERE id = 1',
                    'is_success' => true,
                    'duration' => 0.0001,
                    'duration_str' => $this->dataFormatter->formatDuration(0.0001),
                    'memory' => 1024.0,
                    'memory_str' => $this->dataFormatter->formatBytes(1024.0),
                ),
            )
        );

        $this->equals($correctResult, array($record));
    }

    public function testSummaryProfileMessage()
    {
        $records = array(
            array(
                'message' => '     Time: 0.100ms | Memory:  1MB | Memory Delta: +1.0kB | Memory Peak:  2MB | SELECT id, first_name, last_name FROM author WHERE id = 1',
                'context' => 'propel',
                'level' => 200,
                'level_name' => 'info',
                'channel' => 'propel',
                'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
                'extra' => array(),
            ),
            array(
                'message' => '     Time: 0.100ms | Memory:  1MB | Memory Delta: +1.0kB | Memory Peak:  2MB | SELECT id, first_name, last_name FROM author WHERE id = 1',
                'context' => 'propel',
                'level' => 200,
                'level_name' => 'info',
                'channel' => 'propel',
                'datetime' => \DateTime::createFromFormat('U.u', sprintf('%.6F', microtime(true))),
                'extra' => array(),
            ),
        );

        $correctResult = array(
            'nb_statements' => 2,
            'nb_failed_statements' => 0,
            'accumulated_duration' => 0.0002,
            'accumulated_duration_str' => $this->dataFormatter->formatDuration(0.0002),
            'memory_usage' => 2048,
            'memory_usage_str' => $this->dataFormatter->formatBytes(2048),
            'statements' =>  array(
                array(
                    'sql' => 'SELECT id, first_name, last_name FROM author WHERE id = 1',
                    'is_success' => true,
                    'duration' => 0.0001,
                    'duration_str' => $this->dataFormatter->formatDuration(0.0001),
                    'memory' => 1024.0,
                    'memory_str' => $this->dataFormatter->formatBytes(1024.0),
                ),
                array(
                    'sql' => 'SELECT id, first_name, last_name FROM author WHERE id = 1',
                    'is_success' => true,
                    'duration' => 0.0001,
                    'duration_str' => $this->dataFormatter->formatDuration(0.0001),
                    'memory' => 1024.0,
                    'memory_str' => $this->dataFormatter->formatBytes(1024.0),
                ),
            )
        );
        $this->equals($correctResult, $records);
    }
}

class MockHandler
{

    protected $records = array();
    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }
}
