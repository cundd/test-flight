<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 20:46
 */

namespace Cundd\TestFlight;


use Cundd\TestFlight\Output\Printer;
use ErrorException;

/**
 * Class that invokes the test methods
 *
 * @package Cundd\TestFlight
 */
class TestRunner
{
    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var int
     */
    private $successes = 0;

    /**
     * @var int
     */
    private $failures = 0;

    /**
     * TestRunner constructor.
     *
     * @param ClassLoader   $classLoader
     * @param ObjectManager $objectManager
     */
    public function __construct(ClassLoader $classLoader, ObjectManager $objectManager)
    {
        $this->classLoader = $classLoader;
        $this->objectManager = $objectManager;
    }

    /**
     * @param Definition[] $testCollection
     */
    public function runTestDefinitions(array $testCollection)
    {
        foreach ($testCollection as $className => $definitionCollection) {
            $this->runTestDefinitionsForClass($className, $definitionCollection);
        }
        $this->getPrinter()->println('Successful: %d | Failures: %d', $this->successes, $this->failures);
    }

    /**
     * @param string       $className
     * @param Definition[] $definitionCollection
     */
    public function runTestDefinitionsForClass($className, array $definitionCollection)
    {
        $this->getPrinter()->println('Run tests: %s', $className);
        foreach ($definitionCollection as $definition) {
            if ($this->runTestDefinition($definition)) {
                $this->successes += 1;
            } else {
                $this->failures += 1;
            }
        }
    }

    /**
     * @param Definition $definition
     * @return bool
     */
    public function runTestDefinition(Definition $definition)
    {
        $this->classLoader->loadClass($definition->getClassName(), $definition->getFile());

        error_clear_last();
        ob_start();
        try {
            if ($definition->getMethodIsStatic()) {
                @$this->runStaticTest($definition);
            } else {
                @$this->runInstanceTest($definition);
            }

            $result = true;
        } catch (\Error $exception) {
            $this->printException($definition, $exception);

            $result = false;
        } catch (\Exception $exception) {
            $this->printException($definition, $exception);

            $result = false;
        }
        ob_end_clean();

        if (!$result) {
            return false;
        }

        $lastError = error_get_last();
        if (!$lastError) {
            $this->printSuccess($definition);

            return true;
        }

        $this->printException($definition, $this->createExceptionFromError($lastError));

        return false;
    }

    /**
     * @param Definition $definition
     * @return bool
     */
    private function runStaticTest(Definition $definition)
    {
        call_user_func([$definition->getClassName(), $definition->getMethodName()]);
    }

    /**
     * @param Definition $definition
     * @return bool
     */
    private function runInstanceTest(Definition $definition)
    {
        $instance = $this->objectManager->createInstanceOfClass($definition->getClassName());
        $methodName = $definition->getMethodName();

        if ($definition->getMethodIsPublic()) {
            $instance->$methodName();
        } else {
            $definition->getReflectionMethod()->setAccessible(true);
            $definition->getReflectionMethod()->invoke($instance);
        }
    }

    /**
     * @param Definition $definition
     * @param \Throwable $exception
     */
    private function printException(Definition $definition, $exception)
    {
        $printer = $this->getPrinter();
        $printer->println(
            'Error during test %s%s%s: #%s %s in %s at %s',
            $definition->getClassName(),
            $definition->getMethodIsStatic() ? '::' : '->',
            $definition->getMethodName(),
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
    }

    /**
     * @param Definition $definition
     */
    private function printSuccess(Definition $definition)
    {
        $printer = $this->getPrinter();
        $printer->println('Run %s', $this->getDescriptionForMethod($definition));
    }

    /**
     * @return Printer
     */
    private function getPrinter()
    {
        return new Printer(STDOUT);
    }

    /**
     * @param array $error
     * @param int   $code
     * @return ErrorException
     */
    private function createExceptionFromError(array $error, $code = 0)
    {
        return new ErrorException($error['message'], $code, $error['type'], $error['file'], $error['line']);
    }

    /**
     * @param Definition $definition
     * @return string
     */
    private function getDescriptionForMethod(Definition $definition)
    {
        return ucfirst(ltrim(strtolower(preg_replace('/[A-Z]/', ' $0', $definition->getMethodName()))));
    }
}
