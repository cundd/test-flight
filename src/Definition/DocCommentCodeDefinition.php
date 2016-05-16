<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 22:34
 */
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
    public function __construct($className, $code, FileInterface $file, $relatedMethodName)
    {
        $this->className = $className;
        $this->code = $code;
        $this->file = $file;
        $this->relatedMethodName = $relatedMethodName;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getRelatedMethodName()
    {
        return $this->relatedMethodName;
    }

    /**
     * Returns a description for the output
     *
     * @return string
     */
    public function getDescription()
    {
        return sprintf(
            'DocComment test "%s"',
            ucwords(
                ltrim(strtolower(preg_replace('/[A-Z]/', ' $0', $this->getRelatedMethodName())))
            )
        );
    }
}
