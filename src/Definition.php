<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 22:34
 */

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
     * @param string           $className
     * @param string           $method
     * @param File             $file
     * @param ReflectionMethod $reflectionMethod
     */
    public function __construct($className, $method, File $file, ReflectionMethod $reflectionMethod = null)
    {
        $this->className = $className;
        $this->methodName = $method;
        $this->file = $file;
        $this->reflectionMethod = $reflectionMethod;
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
    public function getMethodName()
    {
        return $this->methodName;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return boolean
     */
    public function getMethodIsStatic()
    {
        $reflectionMethod = $this->getReflectionMethod();
        if (!$reflectionMethod) {
            return false;
        }
        return $reflectionMethod->getModifiers() & ReflectionMethod::IS_STATIC;
    }

    /**
     * @return boolean
     */
    public function getMethodIsPublic()
    {
        $reflectionMethod = $this->getReflectionMethod();
        if (!$reflectionMethod) {
            return false;
        }
        return $reflectionMethod->getModifiers() & ReflectionMethod::IS_PUBLIC;
    }

    /**
     * @return ReflectionMethod
     */
    public function getReflectionMethod()
    {
        return $this->reflectionMethod;
    }
}
