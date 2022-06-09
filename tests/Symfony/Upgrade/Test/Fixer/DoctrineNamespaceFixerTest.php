<?php

namespace Symfony\Upgrade\Test\Fixer;

use Symfony\Upgrade\Fixer\DoctrineNamespaceFixer;
use PHPUnit\Framework\TestCase;

class DoctrineNamespaceFixerTest extends AbstractFixerTestBase
{
    protected function getFixer(): DoctrineNamespaceFixer
    {
        return new DoctrineNamespaceFixer();
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
            // 14: Doctrioneインポート名前空間の修正
            $this->prepareTestCase('manager-registry-fix/case1-output.php', 'manager-registry-fix/case1-input.php'),
            // 22: DoctrineのRegistryInterface名前空間更新
            $this->prepareTestCase('registry-interface-fix/case1-output.php', 'registry-interface-fix/case1-input.php'),
        ];
    }
}
