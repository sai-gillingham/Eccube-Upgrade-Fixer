<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class ExtendedTypesTypeReturnFix extends ReturnTypeFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $this->upsertReturnType($tokens, '....', 'getExtendedTypes', 'iterable');
        return $tokens->generateCode();
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return "Add iterable type return to getExtendedTypes() class";
    }
}
