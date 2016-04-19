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
use Cundd\TestFlight\Definition;

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
     * @param $classNameToFiles
     * @return Definition[]
     */
    public function createForClasses(array $classNameToFiles)
    {
        $definitionCollection = [];
        foreach ($classNameToFiles as $className => $file) {
            $definitionCollection[$className] = $this->collectDefinitionsForClass($className, $file);
        }

        return $definitionCollection;
    }

    /**
     * @param string $className
     * @param File   $file
     * @return Definition[]
     */
    private function collectDefinitionsForClass($className, File $file)
    {
        $this->classLoader->loadClass($className, $file);
        
        $testMethods = [];
        $reflectionClass = new \ReflectionClass($className);
        foreach ($reflectionClass->getMethods() as $method) {
            if (false !== strpos($method->getDocComment(), Constants::TEST_KEYWORD)) {
                $testMethods[] = new Definition(
                    $className, $method->getName(), $file, $method
                );
            }
        }

        return $testMethods;
    }
}
