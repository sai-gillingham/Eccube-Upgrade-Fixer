<?php

namespace Symfony\Upgrade\Test\Fixer;

use Symfony\Upgrade\Fixer\PDOFunctionUpdateFixer;
use PHPUnit\Framework\TestCase;

class PDOFunctionUpdateFixerTest extends AbstractFixerTestBase
{
    protected function getFixer(): PDOFunctionUpdateFixer
    {
        return new PDOFunctionUpdateFixer();
    }

    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input, $file)
    {
        $this->makeTest($expected, $input, $file);
    }

    public function provideExamples(): array
    {
        return [
            // 9: fetchColumn関数はfetchOneに
            $this->prepareTestCase('fetchColumnFix/case1-output.php', 'fetchColumnFix/case1-input.php'),
            
        ];
    }
}
