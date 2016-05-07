<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 07/05/16
 * Time: 13:51
 */

namespace Cundd\TestFlight\Definition;


use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * Abstract base class for Code based definitions
 */
abstract class AbstractCodeDefinition implements CodeDefinitionInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var FileInterface
     */
    protected $file;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return FileInterface
     */
    public function getFile(): FileInterface
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->getFile()->getPath();
    }
}
