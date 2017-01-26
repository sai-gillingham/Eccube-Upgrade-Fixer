<?php


namespace Symfony\Upgrade\Fixer;


use Symfony\Upgrade\Test\Fixer\AbstractFixerTestBase;

class EccubeFormTypeNamesFixerTest extends AbstractFixerTestBase
{
    protected function getFixer()
    {
        return new EccubeFormTypeNamesFixer();
    }

    /**
     * @dataProvider provideExamples
     */
    public function testFix($expected, $input, $file)
    {
        $this->makeTest($expected, $input, $file);
    }

    public function provideExamples()
    {
        return [
            $this->prepareTestCase('case1-output.php', 'case1-input.php'),
        ];
    }
}
