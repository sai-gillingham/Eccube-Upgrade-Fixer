<?php

namespace Symfony\Upgrade\Test\Fixer;

use Symfony\Upgrade\Fixer\UnitTestFixer;

class UnitTestFixerTest extends AbstractFixerTestBase
{
    protected function getFixer(): UnitTestFixer
    {
        return new UnitTestFixer();
    }

    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input, $file)
    {
        $this->doTest($expected, $input, $file);
    }

    public function provideExamples(): array
    {
        return [
            $this->prepareTestCase('case-output-1.php', 'case-input-1.php'),
            $this->prepareTestCase('case-output-2.php', 'case-input-2.php')
        ];
    }
}
