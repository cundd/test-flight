<?php

declare(strict_types=1);

namespace Cundd\TestFlight\DefinitionProvider;

use Cundd\TestFlight\ClassLoader;
use Cundd\TestFlight\CodeExtractor;
use Cundd\TestFlight\Constants;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\DocCommentCodeDefinition;
use Cundd\TestFlight\Definition\DocumentationCodeDefinition;
use Cundd\TestFlight\Definition\MethodDefinition;
use Cundd\TestFlight\Definition\StaticMethodDefinition;
use Cundd\TestFlight\FileAnalysis\File;
use Cundd\TestFlight\FileAnalysis\FileInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

/**
 * Provider for test definitions of classes containing test methods
 */
final class DefinitionProvider implements DefinitionProviderInterface
{
    /**
     * @var list<string>
     */
    private array $types = [
        Constants::TEST_TYPE_METHOD,
        Constants::TEST_TYPE_DOC_COMMENT,
        Constants::TEST_TYPE_DOCUMENTATION,
    ];

    public function __construct(
        private readonly ClassLoader $classLoader,
        private readonly CodeExtractor $codeExtractor,
    ) {
    }

    public function setTypes(array $types): DefinitionProviderInterface
    {
        $this->types = $types;

        return $this;
    }

    public function createForClasses(array $classNameToFiles): array
    {
        $definitionCollection = [];
        foreach ($classNameToFiles as $className => $file) {
            $definitionCollection[$className] = $this->collectDefinitionsForClass(
                $className,
                $file
            );
        }

        return $definitionCollection;
    }

    public function createForDocumentation(array $files): array
    {
        if (in_array(Constants::TEST_TYPE_DOCUMENTATION, $this->types)) {
            $definitions = [];
            foreach ($files as $file) {
                $key = $this->getTestGroupNameForDocumentationFile($file);
                $definitions[$key] = $this->collectDefinitionsForFile($file);
            }

            return $definitions;
        }

        return [];
    }

    /**
     * @return string
     */
    private function getTestGroupNameForDocumentationFile(FileInterface $file)
    {
        $name = $file->getName();
        if ('readme.' === substr(strtolower($name), 0, 7)) {
            $name = basename($file->getParent()) . ': Readme';
        }

        return $name;
    }

    /**
     * @param class-string $className
     *
     * @return DefinitionInterface[]
     */
    private function collectDefinitionsForClass(
        string $className,
        FileInterface $file,
    ): array {
        $this->classLoader->loadClass($className, $file);

        if ($this->types) {
            $definitions = [];
            if (in_array(Constants::TEST_TYPE_METHOD, $this->types)) {
                $definitions = array_merge(
                    $definitions,
                    $this->collectMethodDefinitionsForClass($className, $file)
                );
            }
            if (in_array(Constants::TEST_TYPE_DOC_COMMENT, $this->types)) {
                $definitions = array_merge(
                    $definitions,
                    $this->collectCodeDefinitionsForClass($className, $file)
                );
            }

            return $definitions;
        }

        return array_merge(
            $this->collectMethodDefinitionsForClass($className, $file),
            $this->collectCodeDefinitionsForClass($className, $file)
        );
    }

    /**
     * @param class-string $className
     *
     * @return list<StaticMethodDefinition|MethodDefinition>
     */
    private function collectMethodDefinitionsForClass(
        string $className,
        FileInterface $file,
    ): array {
        $testMethods = [];
        $reflectionClass = new ReflectionClass($className);
        foreach ($reflectionClass->getMethods() as $method) {
            $hasTestKeyword = str_contains(
                (string) $method->getDocComment(),
                Constants::TEST_KEYWORD
            );
            if ($hasTestKeyword) {
                if ($this->getMethodIsStatic($method)) {
                    $testMethods[] = new StaticMethodDefinition(
                        $className,
                        $method->getName(),
                        $file,
                        $method
                    );
                } else {
                    $testMethods[] = new MethodDefinition(
                        $className,
                        $method->getName(),
                        $file,
                        $method
                    );
                }
            }
        }

        return $testMethods;
    }

    /**
     * @param class-string $className
     *
     * @return DocCommentCodeDefinition[]
     */
    private function collectCodeDefinitionsForClass(
        string $className,
        FileInterface $file,
    ): array {
        $reflectionClass = new ReflectionClass($className);

        $testMethods = $this->collectCodeDefinitionsForClassMethods(
            $className,
            $file,
            $reflectionClass
        );

        $definitionForClass = $this->buildCodeDefinitionForClass(
            $className,
            $file,
            $reflectionClass
        );
        if ($definitionForClass) {
            $testMethods[] = $definitionForClass;
        }

        return $testMethods;
    }

    /**
     * @template T of object
     *
     * @param class-string<T>    $className
     * @param ReflectionClass<T> $reflectionClass,
     *
     * @return DocCommentCodeDefinition[]
     */
    private function collectCodeDefinitionsForClassMethods(
        string $className,
        FileInterface $file,
        ReflectionClass $reflectionClass,
    ) {
        $testMethods = [];
        foreach ($reflectionClass->getMethods() as $method) {
            $docComment = $method->getDocComment();
            if ($docComment) {
                if (false !== strpos($docComment, Constants::EXAMPLE_KEYWORD)
                    || false !== strpos($docComment, Constants::CODE_KEYWORD)
                ) {
                    $testMethods[] = new DocCommentCodeDefinition(
                        $className,
                        $this->codeExtractor->getCodeFromDocComment($docComment),
                        $file,
                        $method->getName()
                    );
                }
            }
        }

        return $testMethods;
    }

    /**
     * @template T of object
     *
     * @param class-string<T>    $className
     * @param ReflectionClass<T> $reflectionClass,
     */
    private function buildCodeDefinitionForClass(
        string $className,
        FileInterface $file,
        ReflectionClass $reflectionClass,
    ): ?DocCommentCodeDefinition {
        $docComment = $reflectionClass->getDocComment();
        if (false === $docComment) {
            return null;
        }
        if (false !== strpos($docComment, Constants::EXAMPLE_KEYWORD)
            || false !== strpos($docComment, Constants::CODE_KEYWORD)
        ) {
            return new DocCommentCodeDefinition(
                $className,
                $this->codeExtractor->getCodeFromDocComment($docComment),
                $file,
                $className
            );
        }

        return null;
    }

    /**
     * @return DefinitionInterface[]
     */
    private function collectDefinitionsForFile(FileInterface $file)
    {
        return array_map(
            function ($code) use ($file) {
                return new DocumentationCodeDefinition($code, $file);
            },
            $this->codeExtractor->getCodeFromDocumentation($file->getContents())
        );
    }

    /**
     * @return bool
     */
    private function getMethodIsStatic(ReflectionMethod $reflectionMethod)
    {
        return 0 < ($reflectionMethod->getModifiers() & ReflectionMethod::IS_STATIC);
    }

    /**
     * @test
     *
     * @phpstan-ignore method.unused
     */
    private static function createForThisClassTest(): void
    {
        $prophet = new \Prophecy\Prophet();
        /** @var ClassLoader $dummy */
        $dummy = $prophet->prophesize(ClassLoader::class)->reveal();
        /** @var CodeExtractor $codeExtractor */
        $codeExtractor = $prophet->prophesize(CodeExtractor::class)->reveal();
        $provider = new static($dummy, $codeExtractor);

        $definitions = $provider->createForClasses([__CLASS__ => new File(__FILE__)]);
        test_flight_assert(__CLASS__ === key($definitions));
        test_flight_assert(is_array($definitions[__CLASS__]));
        test_flight_assert(
            current($definitions[__CLASS__]) instanceof \Cundd\TestFlight\Definition\AbstractMethodDefinition
        );
        test_flight_assert(
            current($definitions[__CLASS__]) instanceof StaticMethodDefinition
        );

        test_flight_throws(
            function () use ($provider) {
                /** @var class-string $className */
                $className = 'NotExistingClass'; // @phpstan-ignore varTag.nativeType
                $provider->createForClasses([$className => new File(__FILE__)]);
            },
            ReflectionException::class
        );
    }
}
