<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 22:34
 */

namespace Cundd\TestFlight\Definition;

use Cundd\TestFlight\FileAnalysis\FileInterface;
use ReflectionMethod;

/**
 * Definition of an abstract test defined as a method
 */
abstract class AbstractMethodDefinition implements DefinitionInterface
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
        $className,
        $methodName,
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
     * @return FileInterface
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->getFile()->getPath();
    }

    /**
     * Returns a description for the output
     *
     * @return string
     */
    public function getDescription()
    {
        return ''
        .$this->getType()
        .' '
        .$this->stringToUpperCaseWhitespace(substr(strrchr($this->getClassName(), '\\'), 1))
        .': '
        .$this->stringToUpperCaseWhitespace($this->getMethodName());
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

        return 0 < ($reflectionMethod->getModifiers() & ReflectionMethod::IS_STATIC);
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

        return 0 < ($reflectionMethod->getModifiers() & ReflectionMethod::IS_PUBLIC);
    }

    /**
     * @return ReflectionMethod
     */
    public function getReflectionMethod()
    {
        return $this->reflectionMethod;
    }

    /**
     * @param string $input
     * @return string
     */
    private function stringToUpperCaseWhitespace($input)
    {
        return ucwords(
            ltrim(strtolower(preg_replace('/[A-Z]/', ' $0', $input)))
        );
    }
}
