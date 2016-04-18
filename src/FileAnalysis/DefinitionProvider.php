<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:05
 */

namespace Cundd\TestFlight\FileAnalysis;

use Cundd\TestFlight\ClassLoader;
use Cundd\TestFlight\Constants;
use Cundd\TestFlight\TestDefinition;
use ReflectionMethod;

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
     * DefinitionProvider constructor.
     *
     * @param ClassLoader $classLoader
     */
    public function __construct(ClassLoader $classLoader)
    {
        $this->classLoader = $classLoader;
    }

    /**
     * @param string $directory
     * @return string[]
     */
    public function findInDirectory($directory)
    {
        $definitionCollection = [];
        $provider             = new ClassProvider();
        foreach ($provider->findInDirectory($directory) as $className => $file) {
            $definitionCollection = array_merge(
                $definitionCollection,
                $this->collectDefinitionsForClass($className, $file)
            );
        }

        return $definitionCollection;
    }

    /**
     * @param string $className
     * @param File   $file
     * @return TestDefinition[]
     */
    private function collectDefinitionsForClass($className, File $file)
    {
        $this->classLoader->loadClass($className, $file);

        $testMethods     = [];
        $reflectionClass = new \ReflectionClass($className);
        foreach ($reflectionClass->getMethods() as $method) {
            if (false !== strpos($method->getDocComment(), Constants::TEST_KEYWORD)) {
                $modifiers = $method->getModifiers();
                $testMethods[] = new TestDefinition(
                    $className,
                    $method->getName(),
                    $file,
                    $modifiers & ReflectionMethod::IS_STATIC
                );
            }
        }

        return $testMethods;
    }
}
