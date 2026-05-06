<?php

declare(strict_types=1);

namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\Context\ContextInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\StaticMethodDefinition;

/**
 * Test Runner for static test methods
 */
class StaticMethodTestRunner extends AbstractTestRunner
{
    /**
     * @param StaticMethodDefinition&DefinitionInterface $definition
     */
    protected function performTest(
        DefinitionInterface $definition,
        ContextInterface $context,
    ): void {
        $className = $definition->getClassName();
        $methodName = $definition->getMethodName();

        if ($definition->getMethodIsPublic()) {
            assert(is_callable([$className, $methodName]));
            call_user_func([$className, $methodName], $context);
        } else {
            $method = $definition->getReflectionMethod();
            $method->invoke(null, $context);
        }
    }
}
