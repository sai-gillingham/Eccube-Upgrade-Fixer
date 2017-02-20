<?php


namespace Symfony\Upgrade\Util;


use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;

class ArrayTokenUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testGetArrayKeyTokens()
    {
        $tokens = Tokens::fromCode(<<< EOF
<?php
array(
    'key1' => 'value',
    'key2' => 'value',
    'key3' => 'value',
)
EOF
);

        $actual = ArrayTokenUtil::getArrayKeyTokens($tokens);

        self::assertEquals([
            '4' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key1'", 3]),
            '11' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key2'", 4]),
            '18' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key3'", 5]),
        ], $actual);
    }

    public function testGetArrayKeyTokens_short_array_syntax()
    {
        $tokens = Tokens::fromCode(<<< EOF
<?php
[
    'key1' => 'value',
    'key2' => 'value',
    'key3' => 'value',
]
EOF
        );

        $actual = ArrayTokenUtil::getArrayKeyTokens($tokens);

        self::assertEquals([
            '3' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key1'", 3]),
            '10' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key2'", 4]),
            '17' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key3'", 5]),
        ], $actual);
    }

    public function testGetArrayKeyTokens_with_index()
    {
        $tokens = Tokens::fromCode(<<< EOF
<?php
[
    'key1' => 'value',
    'key2' => 'value',
    'key3' => 'value',
]
EOF
        );

        $actual = ArrayTokenUtil::getArrayKeyTokens($tokens, 1);

        self::assertEquals([
            '3' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key1'", 3]),
            '10' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key2'", 4]),
            '17' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key3'", 5]),
        ], $actual);
    }

    public function testGetArrayKeyTokens_nested()
    {
        $tokens = Tokens::fromCode(<<< EOF
<?php
array(
    'key1' => 'value',
    'key2' => 'value',
    'key3' => array(
        'key31' => 'value',
        'key32' => 'value',
    ),
    'key4' => 'value',
)
EOF
        );

        $actual = ArrayTokenUtil::getArrayKeyTokens($tokens);

        self::assertEquals([
            '4' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key1'", 3]),
            '11' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key2'", 4]),
            '18' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key3'", 5]),
            '42' => new Token([T_CONSTANT_ENCAPSED_STRING, "'key4'", 9]),
        ], $actual);
    }

    public function testGetNextArrayTokenRange()
    {
        $tokens = Tokens::fromCode(<<< EOF
<?php
echo 'Hello'.PHP_EOL;
array(
    'key1' => 'value',
    'key2' => 'value',
    'key3' => array(
        'key31' => 'value',
        'key32' => 'value',
    ),
    'key4' => 'value',
)
EOF
        );

        $actual = ArrayTokenUtil::getNextArrayTokenRange($tokens);

        self::assertEquals(range(8, 56), $actual);
    }

    public function testGetNextArrayTokenRange_without_array()
    {
        $tokens = Tokens::fromCode(<<< EOF
<?php
echo 'Hello'.PHP_EOL;
EOF
        );

        self::assertEquals([], ArrayTokenUtil::getNextArrayTokenRange($tokens));

    }
}
