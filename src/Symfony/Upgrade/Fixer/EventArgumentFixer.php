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
        $changeRequestEventFlag = false;
        $changeResponseEventFlag = false;

        $classFlag = true;

        foreach($tokens as $key => $token){
            // 変数設定前なら次のループへ
            if($classFlag){
                if($token->getContent() == ';'){
                    $changeTarget = $key;
                }
                if($token->getContent() == 'class'){
                    $classFlag = false;
                }
                continue;
            }

            // 変更対象の文字列かを判定
            $checkString = $token->getContent();
            if($checkString == 'GetResponseEvent'){
                // 変更対象ならクラス名を書き換える
                $tokens[$key] = new Token([T_STRING, 'RequestEvent']);

                $changeRequestEventFlag = true;
            }

            if($checkString == 'FilterResponseEvent'){
                // 変更対象ならクラス名を書き換える
                $tokens[$key] = new Token([T_STRING, 'ResponseEvent']);

                $changeResponseEventFlag = true;
            }
        }

        if(!$changeRequestEventFlag && !$changeResponseEventFlag){
            return;
        }

        // クラス名の書き換えがあった場合
        // 型宣言のクラスがUseされているかを確認する
        //使用されていなかった場合
        // useするようにする  Symfony\Component\HttpKernel\Event\RequestEvent
        if($changeRequestEventFlag){
            $fqcn = ['Symfony','Component', 'HttpKernel', 'Event','RequestEvent'];
            if (!$this->hasUseStatements($tokens, $fqcn)) {
                $tokens->insertAt(
                    $changeTarget + 1,
                    array_merge(
                        [
                            new Token([T_WHITESPACE, "\n"]),
                            new Token([T_USE, 'use ']),
                            new Token([T_CLASS, 'Symfony\Component\HttpKernel\Event\RequestEvent']),
                            new Token([T_STRING, ';']),
                            
                            
                        ]
                    )
                );
            }
        }

        if($changeResponseEventFlag){
            $fqcn = ['Symfony','Component', 'HttpKernel', 'Event','ResponseEvent'];
            if (!$this->hasUseStatements($tokens, $fqcn)) {

                $tokens->insertAt(
                    $changeTarget + 1,
                    array_merge(
                        [
                            new Token([T_WHITESPACE, "\n"]),
                            new Token([T_USE, 'use ']),
                            new Token([T_CLASS, 'Symfony\Component\HttpKernel\Event\ResponseEvent']),
                            new Token([T_STRING, ';']),
                            
                        ]
                    )
                );
            }
        }
    }

    public function getDescription()
    {
        return 'Fix Eventristener argument class.';
    }
}