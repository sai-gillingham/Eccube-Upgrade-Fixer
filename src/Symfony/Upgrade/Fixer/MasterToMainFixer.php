<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class MasterToMainFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'masterRequest系のメソッド名を修正します',
            [new CodeSample("isMasterRequest(")],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        while ($this->isGetMasterRequest($tokens)) {
            $this->fixRename1($tokens);
        }

        while ($this->isIsMasterRequest($tokens)){
            $this->fixRename2($tokens);
        }
    }

    private function fixRename1(Tokens $tokens)
    {
        $useTokens = $tokens->findSequence([
            [T_STRING, 'getMasterRequest'],
        ]);
        
        if ($useTokens) {
            $useTokenIndexes = array_keys($useTokens);
            $tokens[$useTokenIndexes[0]] = new Token([T_STRING, 'getMainRequest']);
        }   
    }

    private function fixRename2(Tokens $tokens)
    {
        $useTokens = $tokens->findSequence([
            [T_STRING, 'isMasterRequest'],
        ]);
        
        if ($useTokens) {
            $useTokenIndexes = array_keys($useTokens);
            $tokens[$useTokenIndexes[0]] = new Token([T_STRING, 'isMainRequest']);
        }   
    }

    private function isGetMasterRequest($tokens)
    {
        return $tokens->findSequence([
            [T_STRING, 'getMasterRequest']
        ]);

    }

    private function isIsMasterRequest($tokens)
    {
        return $tokens->findSequence([
            [T_STRING, 'isMasterRequest']
        ]);

    }

    public function getDescription()
    {
        return 'Fix method name "masterRequest" to "mainRequest".';
    }
}