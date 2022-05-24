<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class UnitTestSetUpFixer extends ReturnTypeFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $this->upsertReturnType($tokens, '....', 'setUp', 'void');
        return $tokens->generateCode();
    }

    /**
     * @inheritDoc
     */
    public function getDescription() : string
    {
        return 'UnitTest setUp function requires void return type';
    }
}
