<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class TranslationClassFix extends RenameFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content): string
    {
        $tokens = Tokens::fromCode($content);
        $this->renameFullUseStatements(
            $tokens,
            ['Symfony', 'Component', 'Translation', 'TranslatorInterface'],
            ['Symfony', 'Contracts', 'Translation', 'TranslatorInterface']
        );
        return $tokens->generateCode();
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return "Translation class fixes";
    }
}
