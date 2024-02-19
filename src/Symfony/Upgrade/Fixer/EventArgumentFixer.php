<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class EventArgumentFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'EventListenerのメソッドの引数を修正します',
            [new CodeSample('GetResponseEvent $event')],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // 一番頭のfunctionを基準点として設定する
        $target = $tokens->findSequence([
            [T_FUNCTION]
        ]);
        
        if($target){
            $useTokenIndexes = array_keys($target);
            $targetKey = $useTokenIndexes[0];
        }else{
            return;
        }
        
        foreach($tokens as $key => $token){
            // 変数設定前なら次のループへ
            

            // 変更対象の文字列かを判定

            // 変更対象ならクラス名を書き換える

            // クラス名の書き換えがあった場合

            // 型宣言のクラスがUseされているかを確認する

            //使用されていなかった場合

            // useするようにする
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
        return 'Fix ServiceProvider.';
    }
}