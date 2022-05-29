<?php

namespace Symfony\Upgrade\Fixer;

class PHPDOCFixer extends AbstractFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        // TOKEN層ではコメントが使えないため、生の文字列としてコードを扱うことになる。
        // Due to comments not being available on the TOKEN layer, I will have to handle the code as a raw string.
        $this->_phpDocClassReferenceFix($content, 'var', '\\SwiftMailer', 'MailerInterface');
        $this->_phpDocClassReferenceFix($content, 'param', '\\SwiftMailer', 'MailerInterface');
        $this->_phpDocClassReferenceFix($content, 'param', '\\Doctrine\Common\Persistence\ManagerRegistry', 'ManagerRegistry');
        return $content;
    }

    private function _phpDocClassReferenceFix(string &$fileContent, string $phpDocType, string $classReferenceName, string $replaceReferenceName, ?string $variableIdentifier = null) {
//        var_dump(sprintf("@%s %s", $phpDocType, $classReferenceName));
        if($variableIdentifier == null) {
            $fileContent = str_replace(sprintf("@%s %s", $phpDocType, $classReferenceName), sprintf("@%s %s", $phpDocType, $replaceReferenceName), $fileContent);
        }
    }


    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return "Fixes to incorrect php doc class references";
    }
}
