<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class GetRouteClassFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            'Routeクラスの取得先を変更します',
            [new CodeSample("use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route")],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        if ($this->isHasClass($tokens)) {
            $this->fixUseClass($tokens);
        }
    }

    private function fixUseClass(Tokens $tokens)
    {
        // FindsequenceでRouteクラスを使っているかを判断 ['Sensio','Bundle', 'FrameworkExtraBundle', 'Configration', 'Route'];
        // Symfony\Component\Routing\Annotation\Route;
        $useTokens = $tokens->findSequence([
            [T_USE],
            [T_STRING, 'Sensio'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Bundle'],
            [T_NS_SEPARATOR],
            [T_STRING, 'FrameworkExtraBundle'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Configuration'],
            [T_NS_SEPARATOR],
            [T_STRING, 'Route'],
        ]);

        if ($useTokens) {
            // クラスの取得先を書きかえる
            $useTokenIndexes = array_keys($useTokens);
            $newContent1 = new Token([T_STRING, 'Symfony']);
            $newContent2 = new Token([T_STRING, 'Component']);
            $newContent3 = new Token([T_STRING, 'Routing']);
            $newContent4 = new Token([T_STRING, 'Annotation']);

            $tokens[$useTokenIndexes[1]] = $newContent1;
            $tokens[$useTokenIndexes[3]] = $newContent2;
            $tokens[$useTokenIndexes[5]] = $newContent3;
            $tokens[$useTokenIndexes[7]] = $newContent4;
        }
    }

    
    private function isHasClass($tokens)
    {
        // ネームスペースを区切って見つけたいクラスを発掘する
        $fqcn = ['Sensio','Bundle', 'FrameworkExtraBundle', 'Configuration', 'Route'];
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }else{
            return true;
        }
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}