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

    public function testReplacementParamsContainingBackReferenceSyntaxGeneratesCorrectString()
    {
        $hashedPassword = '$2y$10$S3Y/kSsx8Z5BPtdd9.k3LOkbQ0egtsUHBT9EGQ.spxsmaEWbrxBW2';
        $sql = "UPDATE user SET password = :password";

        $params = array(
            ':password' => $hashedPassword,
        );

        $traced = new TracedStatement($sql, $params);

        $result = $traced->getSqlWithParams();

        $expected = "UPDATE user SET password = <$hashedPassword>";

        $this->assertEquals($expected, $result);
    }

    public function testReplacementParamsContainingPotentialAdditionalQuestionMarkPlaceholderGeneratesCorrectString()
    {
        $hasQuestionMark = "Asking a question?";
        $string = "Asking for a friend";

        $sql = "INSERT INTO questions SET question = ?, detail = ?";

        $params = array($hasQuestionMark, $string);

        $traced = new TracedStatement($sql, $params);

        $result = $traced->getSqlWithParams();

        $expected = "INSERT INTO questions SET question = <$hasQuestionMark>, detail = <$string>";

        $this->assertEquals($expected, $result);

        $result = $traced->getSqlWithParams("'");

        $expected = "INSERT INTO questions SET question = '$hasQuestionMark', detail = '$string'";

        $this->assertEquals($expected, $result);

        $result = $traced->getSqlWithParams('"');

        $expected = "INSERT INTO questions SET question = \"$hasQuestionMark\", detail = \"$string\"";

        $this->assertEquals($expected, $result);
    }

    public function testReplacementParamsContainingPotentialAdditionalNamedPlaceholderGeneratesCorrectString()
    {
        $hasQuestionMark = "Asking a question with a :string inside";
        $string = "Asking for a friend";

        $sql = "INSERT INTO questions SET question = :question, detail = :string";

        $params = array(
            ':question' => $hasQuestionMark,
            ':string'   => $string,
        );

        $traced = new TracedStatement($sql, $params);

        $result = $traced->getSqlWithParams();

        $expected = "INSERT INTO questions SET question = <$hasQuestionMark>, detail = <$string>";

        $this->assertEquals($expected, $result);

        $result = $traced->getSqlWithParams("'");

        $expected = "INSERT INTO questions SET question = '$hasQuestionMark', detail = '$string'";

        $this->assertEquals($expected, $result);

        $result = $traced->getSqlWithParams('"');

        $expected = "INSERT INTO questions SET question = \"$hasQuestionMark\", detail = \"$string\"";

        $this->assertEquals($expected, $result);
    }

    /**
     * Check if query parameters are being replaced in the correct way
     * @bugFix Before fix it : select *
     *                          from geral.person p
     *                           left join geral.contract c
     *                             on c.id_person = p.id_person
     *                           where c.status = <1> and
     *                           p.status <> :status;
     * @return void
     */
    public function testRepeadParamsQuery()
    {
        $sql = 'select *
                from geral.person p
                left join geral.contract c
                  on c.id_person = p.id_person
                where c.status = :status and 
                      p.status <> :status';
        $params = array(
            ':status' => 1
        );
        $traced = new TracedStatement($sql, $params);
        $expected = 'select *
                from geral.person p
                left join geral.contract c
                  on c.id_person = p.id_person
                where c.status = <1> and 
                      p.status <> <1>';
        $result = $traced->getSqlWithParams();
        $this->assertEquals($expected, $result);
    }
}
