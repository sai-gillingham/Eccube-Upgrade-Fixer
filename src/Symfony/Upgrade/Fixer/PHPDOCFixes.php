<?php

namespace Symfony\Upgrade\Fixer;

class PHPDOCFixes extends AbstractFixer
{

    /**
     * @inheritDoc
     */
    public function fix(\SplFileInfo $file, $content)
    {
        if($file->getFilename() == 'MailMagazineService.php') {
            var_dump($content);
        }
        // Due to comments not being available on the TOKEN layer, I will have to handle the code as a raw string.
        $this->_phpDocClassReferenceFix($content, 'var', '\\SwiftMailer', 'MailerInterface');
        $this->_phpDocClassReferenceFix($content, 'param', '\\SwiftMailer', 'MailerInterface');
        $this->_phpDocClassReferenceFix($content, 'param', '\\Doctrine\Common\Persistence\ManagerRegistry', 'ManagerRegistry');
        if($file->getFilename() == 'MailMagazineService.php') {
            var_dump($content);
        }
        //        // TODO: Implement fix() method.
        return $content;
    }

    private function _phpDocClassReferenceFix(string &$fileContent, string $phpDocType, string $classReferenceName, string $replaceReferenceName, ?string $variableIdentifier = null) {
        var_dump(sprintf("@%s %s", $phpDocType, $classReferenceName));
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