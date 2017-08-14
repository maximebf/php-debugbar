<?php

namespace DebugBar\Tests;

use DebugBar\DataCollector\PDO\TracedStatement;

/**
 * Class TracedStatementTest
 * @package DebugBar\Tests
 */
class TracedStatementTest extends DebugBarTestCase
{
    /**
     * Check if query parameters are being replaced in the correct way
     * @bugFix Before fix it : select *
     *                          from geral.exame_part ep
     *                           where ep.id_exame = <1> and
     *                             ep.id_exame_situacao = <2>'
     *                            ep.id_exame_situacao = <1>_situacao
     * @return void
     */
    public function testReplacementParamsQuery()
    {
        $sql = 'select *
                from geral.exame_part ep
                where ep.id_exame = :id_exame and 
                      ep.id_exame_situacao = :id_exame_situacao';
        $params = array(
            ':id_exame'          => 1,
            ':id_exame_situacao' => 2
        );
        $traced = new TracedStatement($sql, $params);
        $expected = 'select *
                from geral.exame_part ep
                where ep.id_exame = <1> and 
                      ep.id_exame_situacao = <2>';
        $result = $traced->getSqlWithParams();
        $this->assertEquals($expected, $result);
    }
}