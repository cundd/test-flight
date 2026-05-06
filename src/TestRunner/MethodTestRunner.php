<?php

declare(strict_types=1);

namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\Context\ContextInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\MethodDefinition;
use ReflectionMethod;

/**
 * Test Runner for test methods
 */
class MethodTestRunner extends AbstractTestRunner
{
    /**
     * @param MethodDefinition&DefinitionInterface $definition
     */
    protected function performTest(
        DefinitionInterface $definition,
        ContextInterface $context,
    ): void {
        $instance = $this->objectManager->create($definition->getClassName());
        $this->invokeSetup($instance);
        $methodName = $definition->getMethodName();

        if ($definition->getMethodIsPublic()) {
            $instance->$methodName($context);
        } else {
            $method = $definition->getReflectionMethod();
            $method->invoke($instance, $context);
        }
    }

    /**
     * @template T of object
     *
     * @param T $instance
     */
    protected function invokeSetup(object $instance): void
    {
        if (!method_exists($instance, 'setUp')) {
            return;
        }

        if (is_callable([$instance, 'setUp'])) {
            $instance->setUp();
        } else {
            $setUpMethodReflection = new ReflectionMethod($instance, 'setUp');
            $setUpMethodReflection->invoke($instance);
        }
    }
}
