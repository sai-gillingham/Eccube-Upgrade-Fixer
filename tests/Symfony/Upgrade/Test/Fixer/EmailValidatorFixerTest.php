<?php

namespace Symfony\Upgrade\Test\Fixer;

use Symfony\Upgrade\Fixer\EmailValidatorFixer;
use PHPUnit\Framework\TestCase;

class EmailValidatorFixerTest extends AbstractFixerTestBase
{
    protected function getFixer(): EmailValidatorFixer
    {
        return new EmailValidatorFixer();
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
            // 10: メール検証ロジックの変更
            $this->prepareTestCase('strict_validation_mode_fix/case1-output.php', 'strict_validation_mode_fix/case1-input.php'),
        ];
    }
}
