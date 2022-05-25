<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class DoctrineNamespaceFixer extends RenameFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);
        $this->renameFullUseStatements($tokens, ['Doctrine', 'Common', 'Persistence', 'ManagerRegistry'], ['Doctrine', 'Persistence', 'ManagerRegistry']);
        return $tokens->generateCode();
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return "Update Doctrine namespacing";
    }
}
