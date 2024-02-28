<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class ContainerClassFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'コンテナクラスの取得先を変更します', //説明
            [new CodeSample("Symfony\Component\DependencyInjection\ContainerInterface")], //変更前
            null, //詳細説明
            'services.yamlの追記は未対応です' // 注意点
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if ($this->isHasContainerInterface($tokens)) {
            $this->fixServiceInterface($tokens);
        }
    }

    private function fixServiceInterface(Tokens $tokens)
    {

        /*
         *['Symfony','Component', 'DependencyInjection', 'ContainerInterface'];
         *Symfony\Component\DependencyInjection\ContainerInterface; Psr\Container\ContainerInterface;
         * Silex\ServiceProviderInterface -> Pimple\ServiceProviderInterface に変更
         */
        $useTokens = $tokens->findSequence([
            [T_USE],
            [T_STRING, 'Symfony'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Component'],
            [T_NS_SEPARATOR],
            [T_STRING, 'DependencyInjection'],
            [T_NS_SEPARATOR],
            [T_STRING, 'ContainerInterface']
        ]);

        
        if ($useTokens) {
            // $useTokensの一個目の名前空間の削除
            $useTokenIndexes = array_keys($useTokens);

            // スライドしてきた一個目の名前空間をPsrに変更
            // 二個目をContainerに変
            $newContent1 = new Token([T_STRING, 'Psr']);
            $newContent2 = new Token([T_STRING, 'Container']);

            $tokens->clearTokenAndMergeSurroundingWhitespace($useTokenIndexes[1]);
            $tokens->clearTokenAndMergeSurroundingWhitespace($useTokenIndexes[2]);
            $tokens[$useTokenIndexes[3]] = $newContent1;
            $tokens[$useTokenIndexes[5]] = $newContent2;

        }
    }

    private function isHasContainerInterface($tokens)
    {
        // ネームスペースを区切って見つけたいクラスを発掘する
        $fqcn = ['Symfony','Component', 'DependencyInjection', 'ContainerInterface'];
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }else{
            return true;
        }
    }

    public function getDescription()
    {
        return 'Fix using ContainerClass namespace.';
    }
}