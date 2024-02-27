<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class NewTokenFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Symfony6.0更新による引数の変更に対応します',
            [new CodeSample('new UsernamePasswordToken($Customer[0], null, "customer", ["ROLE_USER"])')],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // 問題となるトークンクラスを使っていないなら判定する必要は無い
        $fqcn1 = ['Symfony','Component', 'Security', 'Core', 'Authentication', 'Token', 'UsernamePasswordToken'];
        $fqcn2 = ['Symfony','Component', 'Security', 'Core', 'Authentication', 'Token', 'PreAuthenticatedToken'];
        $fqcn3 = ['Symfony','Component', 'Security', 'Core', 'Authentication', 'Token', 'SwitchUserToken'];
        if(!$this->hasUseStatements($tokens, $fqcn1) && !$this->hasUseStatements($tokens, $fqcn2) && !$this->hasUseStatements($tokens, $fqcn3)){
            return;
        }

        // ファイル内の全トークンを判定する
        $changeFlag = false;
        foreach($tokens as $key => $token){
            if($token->getContent() == "new" && (
                    $tokens[$key + 2]->getContent() == 'UsernamePasswordToken' ||
                    $tokens[$key + 2]->getContent() == 'PreAuthenticatedToken' ||
                    $tokens[$key + 2]->getContent() == 'SwitchUserToken'
                    )){
                
                // 引数の判定を実行
                $bracketCount = 1;
                $commaCount = 0;
                $secondArgumentsFlag = false;
                $deleteFlag = false;
                $deleteArrayKeys = [];
                for($i = 4; $bracketCount > 0; $i++){
                    if($secondArgumentsFlag){
                        //２個目の引数の場合はキーを保存する
                        $deleteArrayKeys[] = $key + $i;
                    }
                    // かっこが増えた場合
                    if($tokens[$key + $i]->getContent() == "("){
                        $bracketCount = $bracketCount + 1;
                    }
                    // かっこが減った場合
                    if($tokens[$key + $i]->getContent() == ")"){
                        $bracketCount = $bracketCount - 1;
                    }
                    // コンマだった場合 かっこカウントが２以上の場合は加算させるとまずい
                    if($tokens[$key + $i]->getContent() == "," && $bracketCount == 1){
                        $commaCount++;
                        if($commaCount == 1){
                            // ここに入る場合次からのループは２個目の引数
                            $secondArgumentsFlag = true;
                        }
                        if($commaCount == 2){
                            // ここに入る場合は次からのループは３個目の引数
                            $secondArgumentsFlag = false;
                        }
                        if($commaCount == 3){
                            // ここに入る場合引数が４個あるので削除フラグを立ててforループは閉じさせる
                            $deleteFlag = true;
                            break;
                        }
                    }
                }

                // ここに飛ぶ段階でかっこは閉じられた
                if($deleteFlag){
                    $changeFlag = true;
                    //引数が４個なので２個目の引数を削除する
                    foreach($deleteArrayKeys as $deleteKey){
                        $tokens->clearTokenAndMergeSurroundingWhitespace($deleteKey);
                    }
                }
            }
        }
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}