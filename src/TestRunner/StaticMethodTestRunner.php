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
 * Test Runner for static test methods
 */
class StaticMethodTestRunner extends AbstractTestRunner
{
    /**
     * @param MethodDefinition|DefinitionInterface $definition
     * @return void
     */
    protected function performTest(DefinitionInterface $definition)
    {
        $className = $definition->getClassName();
        $methodName = $definition->getMethodName();

        if ($definition->getMethodIsPublic()) {
            call_user_func([$className, $methodName]);
        } else {
            $method = new \ReflectionMethod($className, $methodName);
            $method->setAccessible(true);
            $method->invoke(null);
        }
    }

}