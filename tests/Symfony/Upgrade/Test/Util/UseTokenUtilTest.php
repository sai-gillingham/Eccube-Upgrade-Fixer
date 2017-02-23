<?php


namespace Symfony\Upgrade\Util;


use Symfony\CS\Tokenizer\Tokens;

class UseTokenUtilTest extends \PHPUnit_Framework_TestCase
{

    public function testGetClassNameMap()
    {
        $tokens = Tokens::fromCode(<<< EOT
<?php

namespace Hoge;

use Eccube\Application;
use Silex\Application as BaseApplication;
use \Silex\ServiceProviderInterface;

class Hoge
{
}
EOT
);

        $actual = UseTokenUtil::getClassNameMap($tokens);

        self::assertEquals([
            'Application' => ['Eccube', 'Application'],
            'BaseApplication' => ['Silex', 'Application'],
            'ServiceProviderInterface' => ['Silex', 'ServiceProviderInterface']
        ], $actual);
    }

}
