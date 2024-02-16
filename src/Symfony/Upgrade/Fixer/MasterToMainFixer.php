<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class MasterToMainFixer extends AbstractFixer
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
        while ($this->isGetMainRequest($tokens)) {
            $this->fixRename($tokens);
        }
        //return $tokens->generateCode();
        file_put_contents($file, $tokens->generateCode());
    }

    private function fixRename(Tokens $tokens)
    {

        /*
         * Silex\ServiceProviderInterface -> Pimple\ServiceProviderInterface に変更
         */
        $useTokens = $tokens->findSequence([
            [T_STRING, 'getMasterRequest'],
        ]);
        
        if ($useTokens) {
            $useTokenIndexes = array_keys($useTokens);
            $tokens[$useTokenIndexes[0]] = new Token([T_STRING, 'getMainRequest']);
        }   
    }

    private function isGetMainRequest($tokens)
    {
        return $tokens->findSequence([
            [T_STRING, 'getMasterRequest']
        ]);

    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}