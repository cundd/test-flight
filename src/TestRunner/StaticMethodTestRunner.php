<?php

declare(strict_types=1);

namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\Context\ContextInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\StaticMethodDefinition;
use Cundd\TestFlight\ErrorBoundary;
use Cundd\TestFlight\Result;

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
    ): Result {
        $className = $definition->getClassName();
        $methodName = $definition->getMethodName();
        $exceptionBoundary = new ErrorBoundary();
        if ($definition->getMethodIsPublic()) {
            assert(is_callable([$className, $methodName]));

            return $exceptionBoundary->execute(
                $className::$methodName(...),
                $context
            );
        } else {
            $method = $definition->getReflectionMethod();

            return $exceptionBoundary->execute(
                $method->invoke(...),
                null,
                $context
            );
        }
    }
}
