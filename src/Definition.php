<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 22:34
 */
declare(strict_types = 1);

namespace Cundd\TestFlight;

use Cundd\TestFlight\FileAnalysis\File;
use ReflectionMethod;

/**
 * Definition of a test
 *
 * @package Cundd\TestFlight
 */
class Definition
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $methodName;

    /**
     * @var File
     */
    private $file;

    /**
     * @var ReflectionMethod
     */
    private $reflectionMethod;

    /**
     * TestDefinition constructor.
     *
     * @param string $className
     * @param string $methodName
     * @param File $file
     * @param ReflectionMethod $reflectionMethod
     */
    public function __construct(
        string $className,
        string $methodName,
        File $file,
        ReflectionMethod $reflectionMethod = null
    ) {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->file = $file;
        $this->reflectionMethod = $reflectionMethod;
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
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }

    /**
     * @return boolean
     */
    public function getMethodIsStatic(): bool
    {
        $reflectionMethod = $this->getReflectionMethod();
        if (!$reflectionMethod) {
            return false;
        }

        return 0 < ($reflectionMethod->getModifiers() & ReflectionMethod::IS_STATIC);
    }

    /**
     * @return boolean
     */
    public function getMethodIsPublic(): bool
    {
        $reflectionMethod = $this->getReflectionMethod();
        if (!$reflectionMethod) {
            return false;
        }

        return 0 < ($reflectionMethod->getModifiers() & ReflectionMethod::IS_PUBLIC);
    }

    /**
     * @return ReflectionMethod
     */
    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }
}
