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
use Cundd\TestFlight\Definition\StaticMethodDefinition;

/**
 * Test Runner for static test methods
 */
class StaticMethodTestRunner extends AbstractTestRunner
{
    /**
     * @param StaticMethodDefinition|DefinitionInterface $definition
     * @param ContextInterface                           $context
     */
    protected function performTest(DefinitionInterface $definition, ContextInterface $context)
    {
        $className = $definition->getClassName();
        $methodName = $definition->getMethodName();

        if ($definition->getMethodIsPublic()) {
            call_user_func([$className, $methodName], $context);
        } else {
            $method = $definition->getReflectionMethod();
            $method->setAccessible(true);
            $method->invoke(null, $context);
        }
    }
}
