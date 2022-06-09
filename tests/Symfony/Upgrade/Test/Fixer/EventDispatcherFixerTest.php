<?php

namespace Symfony\Upgrade\Test\Fixer;

use Symfony\Upgrade\Fixer\EventDispatcherFixer;

class EventDispatcherFixerTest extends AbstractFixerTestBase
{
    protected function getFixer(): EventDispatcherFixer
    {
        return new EventDispatcherFixer();
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
            // 10: eventDispatcher引数の順番変更
            $this->prepareTestCase('event-dispatch-parameter-fix/case1-output.php', 'event-dispatch-parameter-fix/case1-input.php'),
        ];
    }
}
