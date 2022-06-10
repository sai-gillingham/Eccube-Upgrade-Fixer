<?php

namespace Symfony\Upgrade\Test\Fixer;

use Symfony\Upgrade\Fixer\RemoveFormatFromDateFormFixer;
use PHPUnit\Framework\TestCase;

class RemoveFormatFromDateFormFixerTest extends AbstractFixerTestBase
{
    protected function getFixer(): RemoveFormatFromDateFormFixer
    {
        return new RemoveFormatFromDateFormFixer();
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
            $this->prepareTestCase('remove-format-fixer/case1-output.php', 'remove-format-fixer/case1-input.php'),
        ];
    }
}
