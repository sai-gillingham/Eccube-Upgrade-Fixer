<?php

namespace Symfony\Upgrade\Test\Fixer;

use Symfony\Upgrade\Fixer\TranslationClassFixer;
use PHPUnit\Framework\TestCase;

class TranslationClassFixerTest extends AbstractFixerTestBase
{
    protected function getFixer(): TranslationClassFixer
    {
        return new TranslationClassFixer();
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
            // 15: TranslatorInterfaceの名前空間の変更
            $this->prepareTestCase('symfony-component-fix/case1-output.php', 'symfony-component-fix/case1-input.php'),
        ];
    }
}
