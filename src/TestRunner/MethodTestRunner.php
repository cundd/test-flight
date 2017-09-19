<?php
declare(strict_types=1);


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
     */
    protected function performTest(DefinitionInterface $definition, ContextInterface $context)
    {
        $instance = $this->objectManager->create($definition->getClassName());
        $this->invokeSetup($instance);
        $methodName = $definition->getMethodName();

        if ($definition->getMethodIsPublic()) {
            $instance->$methodName($context);
        } else {
            $method = $definition->getReflectionMethod();
            $method->setAccessible(true);
            $method->invoke($instance, $context);
        }
    }

    /**
     * @param $instance
     */
    protected function invokeSetup($instance)
    {
        if (!method_exists($instance, 'setUp')) {
            return;
        }

        if (is_callable([$instance, 'setUp'])) {
            $instance->setUp();
        } else {
            $setUpMethodReflection = new \ReflectionMethod(get_class($instance), 'setUp');
            $setUpMethodReflection->setAccessible(true);
            $setUpMethodReflection->invoke($instance);
        }
    }
}
