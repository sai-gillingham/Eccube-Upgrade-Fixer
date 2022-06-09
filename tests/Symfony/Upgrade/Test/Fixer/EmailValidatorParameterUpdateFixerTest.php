<?php

namespace Symfony\Upgrade\Test\Fixer;

use Symfony\Upgrade\Fixer\EmailValidatorParameterUpdateFixer;
use PHPUnit\Framework\TestCase;

class EmailValidatorParameterUpdateFixerTest extends AbstractFixerTestBase
{
    protected function getFixer(): EmailValidatorParameterUpdateFixer
    {
        return new EmailValidatorParameterUpdateFixer();
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
            $this->prepareTestCase('email-parameter-fix/case1-output.php', 'email-parameter-fix/case1-input.php'),
        ];
    }
}
