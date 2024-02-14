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
            'Converts simple usages of `array_push($x, $y);` to `$x[] = $y;`.',
            [new CodeSample("<?php\narray_push(\$x, \$y);\n")],
            null,
            'Risky when the function `array_push` is overridden.'
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {

        $flag = false;
        while ($this->isHasContainerInterface($tokens)) {
            $this->fixServiceInterface($tokens);
            $flag = true;
        }

        if($flag){
            file_put_contents($file, $tokens->generateCode());
        }
        
        //return $tokens->generateCode();
    }

    private function fixServiceInterface(Tokens $tokens)
    {

        //$templateDir = $container->getParameter('eccube_theme_front_dir');

        //$templateDir = $container->get(EccubeConfig::class)->get('eccube_theme_front_dir');


        //getParameterをgetに　変更

        //引数としてEccubeConfigクラスを設定　追加

        //矢印を追加　追加

        //getメソッドを追加　追加

        //$eccubeConfig = $container->get(EccubeConfig::class);
        //$templateDir = $eccubeConfig->get('eccube_theme_front_dir');
        $useTokens = $tokens->findSequence([
            [T_VARIABLE],
            [T_OBJECT_OPERATOR],
            [T_STRING, 'getParameter']
        ]);

        
        if ($useTokens) {
            // $useTokensの一個目の名前空間の削除
            $useTokenIndexes = array_keys($useTokens);

            // スライドしてきた一個目の名前空間をPsrに変更
            // 二個目をContainerに変更
            $changeContent1 = new Token([T_STRING, 'get']);
            
            $tokens[$useTokenIndexes[2]] = $changeContent1;

            // 追加トークンを列挙
            $tokens->insertAt(
                $useTokenIndexes[2] + 1,
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

    /**
     * @param Tokens|$tokens
     */
    private function fixShare($tokens)
    {
        $currentIndex = 0;
        $matchedTokens = null;
        do {
            $matchedTokens = $tokens->findSequence([
                [T_VARIABLE,'$container','$serviceContainer'],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'share'],
                '('
            ], $currentIndex);
            if ($matchedTokens) {
                $matchedIndexes = array_keys($matchedTokens);
                $blockEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, end($matchedIndexes));
                $tokens->clearRange($matchedIndexes[0], end($matchedIndexes));
                $tokens[$blockEnd]->clear();
                $currentIndex = $blockEnd + 1;
            }
        } while ($matchedTokens);
    }

    /**
     * @param Tokens|$tokens
     */
    private function fixExtend($tokens)
    {
        $currentIndex = 0;
        $matchedTokens = null;
        do {
            $matchedTokens = $tokens->findSequence([
                [T_VARIABLE],
                '[',
                [T_CONSTANT_ENCAPSED_STRING],
                ']',
                '=',
                [T_VARIABLE],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'extend']
            ], $currentIndex);
            if ($matchedTokens) {
                $matchedIndexes = array_keys($matchedTokens);
                $assignmentTokenIndex = $matchedIndexes[4];
                $tokens->clearRange($matchedIndexes[0], $assignmentTokenIndex);
                $tokens->removeTrailingWhitespace($assignmentTokenIndex);
                $currentIndex = end($matchedIndexes) + 1;
            }
        } while ($matchedTokens);
    }

    private function isHasContainerInterface($tokens)
    {
        return null !== $tokens->findSequence([
                [T_VARIABLE, '$container','$serviceContainer'],
                [T_OBJECT_OPERATOR],
                [T_STRING, 'getParameter']
            ]);
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}