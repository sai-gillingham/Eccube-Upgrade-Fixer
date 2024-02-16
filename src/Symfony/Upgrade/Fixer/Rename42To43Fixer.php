<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class Rename42To43Fixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'プラグインの名前空間「**42」を「**43」に変換',
            [new CodeSample("Plugin\Sample42\Controller")],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // @todo 名前空間の変更によるpsr7に関するエラーが発生するためいったん未実装状態とする
        //        修正するにはプラグインのディレクトリ名を「**42」から「**43」に変更する必要がある
        return;
        // ファイル内の全トークンを判定する
        $changeFlag = false;
        foreach($tokens as $key => $token){
            if(substr($token->getContent(), -2) == '42' && $tokens[$key + 1]->getContent() == '\\'){
                // **42\ の文字列が見つかった場合は書き換えを実行
                $newString = str_replace('42', '43', $token->getContent());

                $newToken = new Token([T_STRING, $newString]);

                $tokens[$key] = $newToken;
                $changeFlag = true;
            }
        }

        if($changeFlag){
            file_put_contents($file, $tokens->generateCode());
        }
    }

    

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}