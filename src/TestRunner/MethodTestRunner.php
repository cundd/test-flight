<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28/04/16
 * Time: 20:54
 */

namespace Cundd\TestFlight\TestRunner;


use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\MethodDefinition;

/**
 * Test Runner for test methods
 */
class MethodTestRunner extends AbstractTestRunner
{
    /**
     * @param MethodDefinition|DefinitionInterface $definition
     * @return bool
     */
    protected function performTest(DefinitionInterface $definition)
    {
        $instance = $this->objectManager->create($definition->getClassName());
        $methodName = $definition->getMethodName();

        if ($definition->getMethodIsPublic()) {
            $instance->$methodName();
        } else {
            $definition->getReflectionMethod()->setAccessible(true);
            $definition->getReflectionMethod()->invoke($instance);
        }
    }
}
