<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class ServiceProviderFixer extends AbstractFixer
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
        if ($this->isServiceProviderType($tokens)) {
            $this->fixServiceInterface($tokens);
            $this->fixShare($tokens);
            $this->fixExtend($tokens);
        }
        //return $tokens->generateCode();
    }

    private function fixServiceInterface(Tokens $tokens)
    {

        /*
         * Silex\ServiceProviderInterface -> Pimple\ServiceProviderInterface に変更
         */
        $useTokens = $tokens->findSequence([
            [T_USE],
            [T_STRING, 'Silex'],
            [T_NS_SEPARATOR],
            [T_STRING, 'ServiceProviderInterface']
        ]);
        
        if ($useTokens) {
            $useTokenIndexes = array_keys($useTokens);
            $useTokens[$useTokenIndexes[1]]->setContent('Pimple');
        }

        /*
         * register(Appplication $app) -> register(Container $app)に変更
         */
        $registerFunctionTokens = $tokens->findSequence([
            [T_FUNCTION],
            [T_STRING, 'register'],
            '(',
            [T_STRING]
        ]);

        if ($registerFunctionTokens) {
            $registerFunctionTokenIndexes = array_keys($registerFunctionTokens);
            $classNameMap = UseTokenUtil::getClassNameMap($tokens);
            $arg0Token = $registerFunctionTokens[$registerFunctionTokenIndexes[3]];
            $arg0Content = $arg0Token->getContent();
            if (isset($classNameMap[$arg0Content]) && $classNameMap[$arg0Content] == ['Silex', 'Application']) {
                $arg0Token->setContent('Container');
                $this->addUseStatement($tokens, ['Pimple', 'Container']);
            }
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
                [T_VARIABLE],
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

    private function isServiceProviderType($tokens)
    {
        $fqcn = ['Silex', 'ServiceProviderInterface'];
        if (!$this->hasUseStatements($tokens, $fqcn)) {
            return false;
        }

        return null !== $tokens->findSequence([
                [T_CLASS],
                [T_STRING],
                [T_IMPLEMENTS],
                [T_STRING, array_pop($fqcn)],
            ]);
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}