<?php

namespace Symfony\Upgrade\Fixer;

use Symfony\CS\Tokenizer\Tokens;

class EventNamespaceUpdateFixer extends RenameFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content): string
    {
        $tokens = Tokens::fromCode($content);

        // Event
        $this->renameUseStatements($tokens, ['Symfony', 'Component', 'HttpKernel', 'Event', 'FilterControllerEvent'], 'ControllerEvent');
        $this->renameFunctionParameterTypes($tokens, 'FilterControllerEvent', 'ControllerEvent');

        $this->renameUseStatements($tokens, ['Symfony', 'Component', 'HttpKernel', 'Event', 'GetResponseForExceptionEvent'], 'ExceptionEvent');
        $this->renameFunctionParameterTypes($tokens, 'GetResponseForExceptionEvent', 'ExceptionEvent');

        $this->renameUseStatements($tokens, ['Symfony', 'Component', 'HttpKernel', 'Event', 'PostResponseEvent'], 'TerminateEvent');
        $this->renameFunctionParameterTypes($tokens, 'PostResponseEvent', 'TerminateEvent');

        $this->renameUseStatements($tokens, ['Symfony', 'Component', 'HttpKernel', 'Event', 'FilterResponseEvent'], 'ResponseEvent');
        $this->renameFunctionParameterTypes($tokens, 'FilterResponseEvent', 'ResponseEvent');

        $this->renameUseStatements($tokens, ['Symfony', 'Component', 'HttpKernel', 'Event', 'GetResponseEvent'], 'RequestEvent');
        $this->renameFunctionParameterTypes($tokens, 'GetResponseEvent', 'RequestEvent');

        return $tokens->generateCode();
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return "Response event namespace update.";
    }
}