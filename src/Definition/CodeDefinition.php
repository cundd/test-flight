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
 *
 * @package Cundd\TestFlight
 */
class CodeDefinition implements DefinitionInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $code;

    /**
     * @var FileInterface
     */
    private $file;

    /**
     * CodeDefinition constructor.
     *
     * @param string        $className
     * @param string        $code
     * @param FileInterface $file
     */
    public function __construct(string $className, string $code, FileInterface $file)
    {
        $this->className = $className;
        $this->code = $code;
        $this->file = $file;
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
