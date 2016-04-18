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
     * TestRunner constructor.
     *
     * @param ClassLoader   $classLoader
     * @param ObjectManager $objectManager
     */
    public function __construct(ClassLoader $classLoader, ObjectManager $objectManager)
    {
        $this->classLoader   = $classLoader;
        $this->objectManager = $objectManager;
    }

    /**
     * @param TestDefinition[] $testCollection
     */
    public function runTestDefinitions(array $testCollection)
    {
        $successes = 0;
        $failures  = 0;
        foreach ($testCollection as $definition) {
            if ($this->runTestDefinition($definition)) {
                $successes += 1;
            } else {
                $failures += 1;
            }
        }

        $this->getPrinter()->println('Successful: %d | Failures: %d', $successes, $failures);
    }

    /**
     * @param TestDefinition $definition
     * @return bool
     */
    public function runTestDefinition(TestDefinition $definition)
    {
        $this->classLoader->loadClass($definition->getClassName(), $definition->getFile());

        error_clear_last();
        ob_start();
        try {
            if ($definition->isStatic()) {
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
     * @param TestDefinition $definition
     * @return bool
     */
    private function runStaticTest(TestDefinition $definition)
    {
        call_user_func([$definition->getClassName(), $definition->getMethod()]);
    }

    /**
     * @param TestDefinition $definition
     * @return bool
     */
    private function runInstanceTest(TestDefinition $definition)
    {
        $instance   = $this->objectManager->createInstanceOfClass($definition->getClassName());
        $methodName = $definition->getMethod();

        $instance->$methodName();
    }

    /**
     * @param TestDefinition $definition
     * @param \Throwable     $exception
     */
    private function printException(TestDefinition $definition, $exception)
    {
        $printer = $this->getPrinter();
        $printer->println(
            'Error during test %s%s%s: #%s %s in %s at %s',
            $definition->getClassName(),
            $definition->isStatic() ? '::' : '->',
            $definition->getMethod(),
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine()
        );
    }

    /**
     * @param TestDefinition $definition
     */
    private function printSuccess(TestDefinition $definition)
    {
        $printer = $this->getPrinter();
        $printer->println(
            'Success for test %s%s%s',
            $definition->getClassName(),
            $definition->isStatic() ? '::' : '->',
            $definition->getMethod()
        );
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
}
