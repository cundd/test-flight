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
 * Definition of a test defined as Doc Comment
 */
class DocCommentCodeDefinition extends AbstractCodeDefinition
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $relatedMethodName;

    /**
     * CodeDefinition constructor.
     *
     * @param string        $className
     * @param string        $code
     * @param FileInterface $file
     * @param string        $relatedMethodName
     */
    public function __construct(string $className, string $code, FileInterface $file, string $relatedMethodName)
    {
        $this->className = $className;
        $this->code = $code;
        $this->file = $file;
        $this->relatedMethodName = $relatedMethodName;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getRelatedMethodName(): string
    {
        return $this->relatedMethodName;
    }

    /**
     * Returns a description for the output
     *
     * @return string
     */
    public function getDescription(): string
    {
        return sprintf(
            '%s "%s"',
        $this->getType(),
            ucwords(
                ltrim(strtolower(preg_replace('/[A-Z]/', ' $0', $this->getRelatedMethodName())))
            )
        );
    }

    /**
     * Returns the descriptive type of the definition
     *
     * @return string
     */
    public function getType(): string
    {
        return 'DocComment test';
    }
}
