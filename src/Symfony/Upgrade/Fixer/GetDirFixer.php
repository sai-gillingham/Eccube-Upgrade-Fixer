<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class GetDirFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'containerクラスのgetParameterメソッドを変更しEccubeConfigを経由して取得します',
            [new CodeSample('$templateDir = $container->getParameter("eccube_theme_front_dir")')],
            null,
            'コンテナクラスの変数名に「container」もしくは「Container」が含まれる前提となっています'
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // 使用クラスを確認　ContainerInterfaceを使っていないなら判定する必要は無い
        $fqcn = ['Psr','Container', 'ContainerInterface'];
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return;
        }

        $flag = false;

        foreach($tokens as $key => $token){
            $content = $token->getContent();

            // **container->getParameterの並びを抽出する
            if(!str_contains($content, 'Container' && !str_contains($content, 'container'))){
                continue;
            }

            if($tokens[$key + 1]->getContent() !== '->'){
                continue;
            }

            if($tokens[$key + 2]->getContent() !== 'getParameter'){
                continue;
            }
            $flag = true;

            // 3個目をContainerに変更
            $changeContent1 = new Token([T_STRING, 'get']);
            $tokens[$key + 2] = $changeContent1;

            // 追加トークンを列挙
            $tokens->insertAt(
                $key + 3,
                array_merge(
                    [
                        new Token([T_STRING, '(']),
                        new Token([T_CLASS, '\Eccube\Common\EccubeConfig::class']),
                        new Token([T_STRING, ')']),
                        new Token([T_OBJECT_OPERATOR, '->']),
                        new Token([T_STRING, 'get']),
                    ]
                )
            );
        }
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}