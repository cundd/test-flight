<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28/04/16
 * Time: 20:54
 */

namespace Cundd\TestFlight\TestRunner;


use Cundd\TestFlight\Context\ContextInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\MethodDefinition;

/**
 * Test Runner for test methods
 */
class MethodTestRunner extends AbstractTestRunner
{
    /**
     * @param MethodDefinition|DefinitionInterface $definition
     * @param ContextInterface                     $context
     * @return bool
     */
    protected function performTest(DefinitionInterface $definition, ContextInterface $context)
    {
        $instance = $this->objectManager->create($definition->getClassName());
        $methodName = $definition->getMethodName();

        if ($definition->getMethodIsPublic()) {
            $instance->$methodName($context);
        } else {
            $method = $definition->getReflectionMethod();
            $method->setAccessible(true);
            $method->invoke($instance, $context);
        }
    }
}
