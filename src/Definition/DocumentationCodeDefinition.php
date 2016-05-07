<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 22:34
 */
declare(strict_types = 1);

namespace Cundd\TestFlight\Definition;

use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * Definition of a test defined as a block of code
 */
class DocumentationCodeDefinition extends AbstractCodeDefinition
{
    /**
     * @param string        $code
     * @param FileInterface $file
     */
    public function __construct(string $code, FileInterface $file)
    {
        $this->code = $code;
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return '';
    }

    /**
     * Returns a description for the output
     *
     * @return string
     */
    public function getDescription(): string
    {
        return sprintf(
            'Documentation test "%s"',
            ucwords(
                ltrim(strtolower(preg_replace('/[A-Z]/', ' $0', $this->getFile()->getName())))
            )
        );
    }
}
