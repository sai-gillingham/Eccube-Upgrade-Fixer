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
        $this->makeTest($expected, $input, $file);
    }

    public function provideExamples(): array
    {
        return [
            // 1: setUp/tearDown関数はpublicタイプをprotectedに
            $this->prepareTestCase('setUpTearDownFix/access_type_fix/case1-output.php', 'setUpTearDownFix/access_type_fix/case1-input.php'),

            // 2: setUp/tearDown関数にvoidリターンタイプの追加
            $this->prepareTestCase('setUpTearDownFix/return_type_fix/case1-output.php', 'setUpTearDownFix/return_type_fix/case1-input.php'),
            $this->prepareTestCase('setUpTearDownFix/return_type_fix/case2-output.php', 'setUpTearDownFix/return_type_fix/case2-input.php'),

            // 3: assertContainsからassertStringContainsStringに
            $this->prepareTestCase('assertFix/assert_contains_fix/case1-output.php', 'assertFix/assert_contains_fix/case1-input.php'),
            
            // 4: assertRegexpからassertMatchesRegularExpressionに
            // @todo: Not sure with issue assertRegExpチェック but will investigate further later
            // $this->prepareTestCase('assertFix/assert_reg_fix/case1-output.php', 'assertFix/assert_reg_fix/case1-input.php')
            
            // 5: $mailCollector = $this->getMailCollector(false);から$Message = $this->getMailerMessage();に
            // @todo: Not sure about the empty spaces after transform
            // $this->prepareTestCase('getMailCollectorMigration/change_mail_collector_instance/case1-output.php', 'getMailCollectorMigration/change_mail_collector_instance/case1-input.php')
            
            // 6: getMailerMessageのgetBody()からgetHtmlBody()に
            // @todo: Not sure about the empty spaces after transform
            // $this->prepareTestCase('getMailCollectorMigration/body_to_html_body/case1-output.php', 'getMailCollectorMigration/body_to_html_body/case1-input.php'),
            $this->prepareTestCase('getMailCollectorMigration/body_to_html_body/case2-output.php', 'getMailCollectorMigration/body_to_html_body/case2-input.php'),
            
            // 7: symfony4のテストログイン処理からsymfony5のログイン処理に
            $this->prepareTestCase('login_process/login_user_update/case1-output.php', 'login_process/login_user_update/case1-input.php'),
            // 8: (assertArraySubset()、 assertEquals() )はassertSame()に
            $this->prepareTestCase('assertFix/assert_same_fix/case1-output.php', 'assertFix/assert_same_fix/case1-input.php'),
            $this->prepareTestCase('assertFix/assert_same_fix/case2-output.php', 'assertFix/assert_same_fix/case2-input.php')
        ];
    }
}
