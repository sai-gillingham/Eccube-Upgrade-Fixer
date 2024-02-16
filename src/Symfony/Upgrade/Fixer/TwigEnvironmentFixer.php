<?php


namespace Symfony\Upgrade\Fixer;

use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Upgrade\Util\UseTokenUtil;

class TwigEnvironmentFixer extends AbstractFixer
{
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(T_DOC_COMMENT);
    }

    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(
            '\Twig_environmentを\Twig\Environmentに修正します',
            [new CodeSample("\Twig_environment")],
            null,
            null
        );
    }

    public function applyFix(\SplFileInfo $file, Tokens $tokens): void
    {
        // ファイル内の全トークンを判定する
        foreach($tokens as $key => $token){
            if(str_contains($token->getContent(),'Twig_Environment')){
                // クラス名を正式なものに書き換える
                $newString = str_replace('Twig_Environment', 'Twig\Environment',$token->getContent());

                $newToken = new Token([T_STRING, $newString]);
                $tokens[$key] = $newToken;
            }
        }

        file_put_contents($file, $tokens->generateCode());
    }

    public function getDescription()
    {
        return 'Fix ServiceProvider.';
    }
}