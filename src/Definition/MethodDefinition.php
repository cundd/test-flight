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
use ReflectionMethod;

/**
 * Definition of a test defined as a method
 *
 * @package Cundd\TestFlight
 */
class MethodDefinition implements DefinitionInterface
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
     * @var FileInterface
     */
    private $file;

    /**
     * @var ReflectionMethod
     */
    private $reflectionMethod;

    /**
     * TestDefinition constructor.
     *
     * @param string           $className
     * @param string           $methodName
     * @param FileInterface    $file
     * @param ReflectionMethod $reflectionMethod
     */
    public function __construct(
        string $className,
        string $methodName,
        FileInterface $file,
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

    /**
     * Returns a description for the output
     *
     * @return string
     */
    public function getDescription(): string
    {
        return ucwords(
            ltrim(strtolower(preg_replace('/[A-Z]/', ' $0', $this->getMethodName())))
        );
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

        return 0 < ($reflectionMethod->getModifiers(
            ) & ReflectionMethod::IS_STATIC);
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

        return 0 < ($reflectionMethod->getModifiers(
            ) & ReflectionMethod::IS_PUBLIC);
    }

    /**
     * @return ReflectionMethod
     */
    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }


}
