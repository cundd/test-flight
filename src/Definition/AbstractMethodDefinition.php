<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Definition;

use Cundd\TestFlight\FileAnalysis\FileInterface;
use ReflectionMethod;

/**
 * Definition of an abstract test defined as a method
 */
abstract class AbstractMethodDefinition implements DefinitionInterface
{
    public function __construct(
        private readonly string $className,
        private readonly string $methodName,
        private readonly FileInterface $file,
        private readonly ReflectionMethod $reflectionMethod,
    ) {
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getFile(): FileInterface
    {
        return $this->file;
    }

    public function getFilePath(): string
    {
        return $this->getFile()->getPath();
    }

    /**
     * Returns a description for the output
     */
    public function getDescription(): string
    {
        return ''
            . $this->getType()
            . ' '
            . $this->stringToUpperCaseWhitespace((string) substr((string) strrchr($this->getClassName(), '\\'), 1))
            . ': '
            . $this->stringToUpperCaseWhitespace($this->getMethodName());
    }

    public function getMethodIsStatic(): bool
    {
        $reflectionMethod = $this->getReflectionMethod();
        if (!$reflectionMethod) {
            return false;
        }

        return 0 < ($reflectionMethod->getModifiers() & ReflectionMethod::IS_STATIC);
    }

    public function getMethodIsPublic(): bool
    {
        $reflectionMethod = $this->getReflectionMethod();
        if (!$reflectionMethod) {
            return false;
        }

        return 0 < ($reflectionMethod->getModifiers() & ReflectionMethod::IS_PUBLIC);
    }

    public function getReflectionMethod(): ReflectionMethod
    {
        return $this->reflectionMethod;
    }

    /**
     * @return string
     */
    private function stringToUpperCaseWhitespace(string $input)
    {
        return ucwords(
            ltrim(strtolower(preg_replace('/[A-Z]/', ' $0', $input)))
        );
    }
}
