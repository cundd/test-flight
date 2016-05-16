<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:05
 */

namespace Cundd\TestFlight\DefinitionProvider;

use Cundd\TestFlight\ClassLoader;
use Cundd\TestFlight\CodeExtractor;
use Cundd\TestFlight\Constants;
use Cundd\TestFlight\Definition\DocCommentCodeDefinition;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\DocumentationCodeDefinition;
use Cundd\TestFlight\Definition\MethodDefinition;
use Cundd\TestFlight\Definition\StaticMethodDefinition;
use Cundd\TestFlight\FileAnalysis\File;
use Cundd\TestFlight\FileAnalysis\FileInterface;
use ReflectionClass;
use ReflectionMethod;

/**
 * Provider for test definitions of classes containing test methods
 */
class DefinitionProvider implements DefinitionProviderInterface
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * @var string[]
     */
    private $types = [
        Constants::TEST_TYPE_METHOD,
        Constants::TEST_TYPE_DOC_COMMENT,
        Constants::TEST_TYPE_DOCUMENTATION,
    ];

    /**
     * @var CodeExtractor
     */
    private $codeExtractor;

    /**
     * Definition Provider
     *
     * @param ClassLoader   $classLoader
     * @param CodeExtractor $codeExtractor
     */
    public function __construct(ClassLoader $classLoader, CodeExtractor $codeExtractor)
    {
        $this->classLoader = $classLoader;
        $this->codeExtractor = $codeExtractor;
    }

    /**
     * Set the types of tests to run
     *
     * @param string[] $types
     * @return $this
     */
    public function setTypes(array $types)
    {
        $this->types = $types;
    }

    /**
     * Create the test definitions for the given classes
     *
     * @param array $classNameToFiles
     * @return array|\Cundd\TestFlight\Definition\DefinitionInterface[]
     */
    public function createForClasses(array $classNameToFiles): array
    {
        $definitionCollection = [];
        foreach ($classNameToFiles as $className => $file) {
            $definitionCollection[$className] = $this->collectDefinitionsForClass($className, $file);
        }

        return $definitionCollection;
    }

    /**
     * Create the test definitions for the given documentation files
     *
     * @param FileInterface[] $files
     * @return array|\Cundd\TestFlight\Definition\DefinitionInterface[]
     */
    public function createForDocumentation(array $files): array
    {
        if (in_array(Constants::TEST_TYPE_DOCUMENTATION, $this->types)) {
            return array_map([$this, 'collectDefinitionsForFile'], $files);
        }

        return [];
    }

    /**
     * @param string        $className
     * @param FileInterface $file
     * @return DefinitionInterface[]
     */
    private function collectDefinitionsForClass(string $className, FileInterface $file): array
    {
        $this->classLoader->loadClass($className, $file);

        if ($this->types) {
            $definitions = [];
            if (in_array(Constants::TEST_TYPE_METHOD, $this->types)) {
                $definitions = array_merge($definitions, $this->collectMethodDefinitionsForClass($className, $file));
            }
            if (in_array(Constants::TEST_TYPE_DOC_COMMENT, $this->types)) {
                $definitions = array_merge($definitions, $this->collectCodeDefinitionsForClass($className, $file));
            }

            return $definitions;
        }

        return array_merge(
            $this->collectMethodDefinitionsForClass($className, $file),
            $this->collectCodeDefinitionsForClass($className, $file)
        );
    }

    /**
     * @param string        $className
     * @param FileInterface $file
     * @return array
     */
    private function collectMethodDefinitionsForClass(
        string $className,
        FileInterface $file
    ) {
        $testMethods = [];
        $reflectionClass = new ReflectionClass($className);
        foreach ($reflectionClass->getMethods() as $method) {
            if (false !== strpos($method->getDocComment(), Constants::TEST_KEYWORD)) {
                if ($this->getMethodIsStatic($method)) {
                    $testMethods[] = new StaticMethodDefinition(
                        $className, $method->getName(), $file, $method
                    );
                } else {
                    $testMethods[] = new MethodDefinition(
                        $className, $method->getName(), $file, $method
                    );
                }
            }
        }

        return $testMethods;
    }

    /**
     * @param string        $className
     * @param FileInterface $file
     * @return array
     */
    private function collectCodeDefinitionsForClass(
        string $className,
        FileInterface $file
    ) {
        $reflectionClass = new ReflectionClass($className);

        $testMethods = $this->collectCodeDefinitionsForClassMethods($className, $file, $reflectionClass);

        $definitionForClass = $this->buildCodeDefinitionForClass($className, $file, $reflectionClass);
        if ($definitionForClass) {
            $testMethods[] = $definitionForClass;
        }

        return $testMethods;
    }

    /**
     * @param string          $className
     * @param FileInterface   $file
     * @param ReflectionClass $reflectionClass
     * @return array
     */
    private function collectCodeDefinitionsForClassMethods(
        string $className,
        FileInterface $file,
        $reflectionClass
    ) {
        $testMethods = [];
        foreach ($reflectionClass->getMethods() as $method) {
            if (false !== strpos($method->getDocComment(), Constants::EXAMPLE_KEYWORD)
                || false !== strpos($method->getDocComment(), Constants::CODE_KEYWORD)
            ) {
                $testMethods[] = new DocCommentCodeDefinition(
                    $className,
                    $this->codeExtractor->getCodeFromDocComment($method->getDocComment()),
                    $file,
                    $method->getName()
                );
            }
        }

        return $testMethods;
    }

    /**
     * @param FileInterface $file
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
     * @param ReflectionMethod $reflectionMethod
     * @return bool
     */
    private function getMethodIsStatic(ReflectionMethod $reflectionMethod)
    {
        return 0 < ($reflectionMethod->getModifiers() & ReflectionMethod::IS_STATIC);
    }

    /**
     * @test
     */
    protected static function createForThisClassTest()
    {
        $prophet = new \Prophecy\Prophet();
        /** @var ClassLoader $dummy */
        $dummy = $prophet->prophesize(ClassLoader::class)->reveal();
        /** @var CodeExtractor $codeExtractor */
        $codeExtractor = $prophet->prophesize(CodeExtractor::class)->reveal();
        $provider = new static($dummy, $codeExtractor);

        $definitions = $provider->createForClasses([__CLASS__ => new File(__FILE__)]);
        test_flight_assert(key($definitions) === __CLASS__);
        test_flight_assert(is_array($definitions[__CLASS__]));
        test_flight_assert(
            current($definitions[__CLASS__]) instanceof \Cundd\TestFlight\Definition\AbstractMethodDefinition
        );
        test_flight_assert(
            current($definitions[__CLASS__]) instanceof \Cundd\TestFlight\Definition\StaticMethodDefinition
        );

        test_flight_throws(
            function () use ($provider) {
                $provider->createForClasses(['NotExistingClass' => new File(__FILE__)]);
            },
            \ReflectionException::class
        );
    }

    /**
     * @param string          $className
     * @param FileInterface   $file
     * @param ReflectionClass $reflectionClass
     * @return DocCommentCodeDefinition
     */
    private function buildCodeDefinitionForClass(
        string $className,
        FileInterface $file,
        ReflectionClass $reflectionClass
    ) {
        if (false !== strpos($reflectionClass->getDocComment(), Constants::EXAMPLE_KEYWORD)
            || false !== strpos($reflectionClass->getDocComment(), Constants::CODE_KEYWORD)
        ) {
            return new DocCommentCodeDefinition(
                $className,
                $this->codeExtractor->getCodeFromDocComment($reflectionClass->getDocComment()),
                $file,
                $className
            );
        }

        return null;
    }
}
