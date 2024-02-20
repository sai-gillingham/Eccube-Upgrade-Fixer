<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class TestContainerFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'self::$containerをself::getContainer()に修正します',
            [new CodeSample('self::$container')],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if(!str_contains($file->getFilename(), "Test")){
            // テストに関するファイル以外は変更対象外
            return;
        }
        // ファイル内の全トークンを判定する
        while($target = $tokens->findSequence([
            [T_DOUBLE_COLON, '::'],
            [T_VARIABLE,'$container']
        ])){
            $useTokenIndexes = array_keys($target);
            $tokens[$useTokenIndexes[1]] = new Token([T_STRING, 'getContainer()']);
        }
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}