<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 20:46
 */

namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\ClassLoader;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\ObjectManager;
use Cundd\TestFlight\Output\ExceptionPrinterInterface;
use Cundd\TestFlight\Output\Printer;
use Cundd\TestFlight\Output\PrinterInterface;
use ErrorException;

/**
 * Class that invokes the test methods
 */
abstract class AbstractTestRunner implements TestRunnerInterface
{
    /**
     * @var ClassLoader
     */
    protected $classLoader;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * TestRunner
     *
     * @param ClassLoader   $classLoader
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ClassLoader $classLoader,
        ObjectManager $objectManager
    ) {
        $this->classLoader = $classLoader;
        $this->objectManager = $objectManager;
    }

    /**
     * @param DefinitionInterface $definition
     * @return void
     */
    abstract protected function performTest(DefinitionInterface $definition);

    /**
     * @param DefinitionInterface $definition
     * @return bool
     */
    public function runTestDefinition(DefinitionInterface $definition): bool
    {
        $this->prepareTestRunnerForDefinition($definition);

        error_clear_last();
        try {
            $this->performTest($definition);

            $result = true;
        } catch (\Error $exception) {
            $this->printException($definition, $exception);

            $result = false;
        } catch (\Exception $exception) {
            $this->printException($definition, $exception);

            $result = false;
        }

        if (!$result) {
            return false;
        }

        $lastError = error_get_last();
        if (!$lastError) {
            $this->printSuccess($definition);

            return true;
        }

        $this->printException(
            $definition,
            $this->createExceptionFromError($lastError)
        );

        return false;
    }

    /**
     * @param DefinitionInterface $definition
     * @param \Throwable          $exception
     */
    private function printException(DefinitionInterface $definition, $exception)
    {
        $this->getExceptionPrinter()->printException($definition, $exception);
    }

    /**
     * @param DefinitionInterface $definition
     */
    private function printSuccess(DefinitionInterface $definition)
    {
        $printer = $this->getPrinter();
        if ($printer->getVerbose()) {
            $printer->info('Successfully ran %s', $definition->getDescription());
        } else {
            $printer->printf('.');
        }
    }

    /**
     * @return Printer
     */
    private function getPrinter()
    {
        return $this->objectManager->get(
            PrinterInterface::class,
            STDOUT,
            STDERR
        );
    }

    /**
     * @return ExceptionPrinterInterface
     */
    private function getExceptionPrinter()
    {
        return $this->objectManager->get(
            ExceptionPrinterInterface::class,
            STDOUT,
            STDERR
        );
    }

    /**
     * @param array $error
     * @param int   $code
     * @return ErrorException
     */
    private function createExceptionFromError(array $error, $code = 0)
    {
        return new ErrorException(
            $error['message'],
            $code,
            $error['type'],
            $error['file'],
            $error['line']
        );
    }

    /**
     * @param DefinitionInterface $definition
     */
    private function prepareTestRunnerForDefinition(DefinitionInterface $definition)
    {
        if ($definition->getClassName()) {
            $this->classLoader->loadClass(
                $definition->getClassName(),
                $definition->getFile()
            );
        }
    }
}
