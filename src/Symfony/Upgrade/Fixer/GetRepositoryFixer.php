<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class GetRepositoryFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'コンテナからレポジトリクラスを取得する方法を変更します',
            [new CodeSample("get(Eccube\Repository\CustomerRepository)")],
            null,
            'controllerの$this->getは変更対象から除外しています'
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // コンテナを使っていないなら判定する必要は無い
        $fqcn1 = ['Symfony','Component', 'DependencyInjection', 'ContainerInterface'];
        $fqcn2 = ['Psr','Container', 'ContainerInterface'];
        if(!$this->hasUseStatements($tokens, $fqcn1) && !$this->hasUseStatements($tokens, $fqcn2)){
            return;
        }

        // ファイル内の全トークンを判定する
        foreach($tokens as $key => $token){
            if($token->getContent() == "get"){
                // 引数の判定を実行
                if(str_contains($RepositoryClass = $tokens[$key +2]->getContent(), "\Repository\\")){
                    
                    // **Repositoryの文字列を取得
                    $position = strrpos($RepositoryClass, '\\');
                    $ClassName = substr($RepositoryClass, $position + 1);
                    // Repositoryを排除
                    $ClassName = substr($ClassName, 0, -11). '::class';

                    $newContent1 = new Token([T_STRING, '"doctrine.orm.entity_manager"']);

                    $tokens[$key + 2] = $newContent1;

                    $tokens->insertAt(
                        $key + 4,
                        array_merge(
                            [
                                new Token([T_OBJECT_OPERATOR, '->']),
                                new Token([T_STRING, 'getRepository']),
                                new Token([T_STRING, '(']),
                                new Token([T_CLASS, $ClassName]),
                                new Token([T_STRING, ')']),
                            ]
                        )
                    );
                }
            }
        }
    }

    public function getDescription()
    {
        return 'Fix getRepository method system.';
    }
}