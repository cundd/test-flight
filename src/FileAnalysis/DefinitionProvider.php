<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:05
 */

namespace Cundd\TestFlight\FileAnalysis;

use Cundd\TestFlight\ClassLoader;
use Cundd\TestFlight\CodeExtractor;
use Cundd\TestFlight\Constants;
use Cundd\TestFlight\Definition\CodeDefinition;
use Cundd\TestFlight\Definition\MethodDefinition;

/**
 * Provider for test definitions of classes containing test methods
 */
class DefinitionProvider
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * @var string[]
     */
    private $types;

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
     * @param array $classNameToFiles
     * @param array $types
     * @return array|\Cundd\TestFlight\Definition\MethodDefinition[]
     * @throws \Exception
     */
    public function createForClasses(array $classNameToFiles, array $types = array()): array
    {
        $this->types = $types;

        $definitionCollection = [];
        foreach ($classNameToFiles as $className => $file) {
            $definitionCollection[$className] = $this->collectDefinitionsForClass($className, $file);
        }

        return $definitionCollection;
    }

    /**
     * @param string        $className
     * @param FileInterface $file
     * @return MethodDefinition[]
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
        $reflectionClass = new \ReflectionClass($className);
        foreach ($reflectionClass->getMethods() as $method) {
            if (false !== strpos($method->getDocComment(), Constants::TEST_KEYWORD)) {
                $testMethods[] = new MethodDefinition(
                    $className, $method->getName(), $file, $method
                );
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
        $testMethods = [];
        $reflectionClass = new \ReflectionClass($className);
        foreach ($reflectionClass->getMethods() as $method) {
            if (false !== strpos($method->getDocComment(), Constants::EXAMPLE_KEYWORD)) {
                $testMethods[] = new CodeDefinition(
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
        test_flight_assert(current($definitions[__CLASS__]) instanceof MethodDefinition);

        test_flight_throws(
            function () use ($provider) {
                $provider->createForClasses(['NotExistingClass' => new File(__FILE__)]);
            },
            \ReflectionException::class
        );
    }
}
