<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class PDOFunctionUpdateFixer extends RenameFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $this->renameChainMethods($tokens, 'fetchColumn', 'fetchOne');
        return $tokens->generateCode();
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return "fetchOne to fetchRow Update";
    }


}
