<?php
declare(strict_types=1);


namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\ClassLoader;
use Cundd\TestFlight\Context\Context;
use Cundd\TestFlight\Context\ContextInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Environment;
use Cundd\TestFlight\Event\Event;
use Cundd\TestFlight\Event\EventDispatcherInterface;
use Cundd\TestFlight\ObjectManager;
use Cundd\TestFlight\Output\ExceptionPrinterInterface;
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
     * @var PrinterInterface
     */
    protected $printer;

    /**
     * @var ExceptionPrinterInterface
     */
    protected $exceptionPrinter;

    /**
     * @var Environment
     */
    protected $environment;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * TestRunner
     *
     * @param ClassLoader               $classLoader
     * @param ObjectManager             $objectManager
     * @param Environment               $environment
     * @param PrinterInterface          $printer
     * @param ExceptionPrinterInterface $exceptionPrinter
     * @param EventDispatcherInterface  $eventDispatcher
     */
    public function __construct(
        ClassLoader $classLoader,
        ObjectManager $objectManager,
        Environment $environment,
        PrinterInterface $printer,
        ExceptionPrinterInterface $exceptionPrinter,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->classLoader = $classLoader;
        $this->objectManager = $objectManager;
        $this->printer = $printer;
        $this->exceptionPrinter = $exceptionPrinter;
        $this->environment = $environment;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param DefinitionInterface $definition
     * @param ContextInterface    $context
     * @return void
     */
    abstract protected function performTest(DefinitionInterface $definition, ContextInterface $context);

    /**
     * @param DefinitionInterface $definition
     * @return bool
     */
    public function runTestDefinition(DefinitionInterface $definition): bool
    {
        $context = new Context();
        $this->eventDispatcher->dispatch(self::EVENT_TEST_WILL_RUN, new Event($definition, $context));
        $this->prepareTestRunnerForDefinition($definition);
        $exception = null;

        set_error_handler([$this, 'handleError']);
        error_clear_last();
        try {
            $this->performTest($definition, $context);
        } catch (\Error $exception) {
        } catch (\Exception $exception) {
        }
        $this->environment->reset();
        $this->eventDispatcher->dispatch(self::EVENT_TEST_DID_RUN, new Event($definition, $context));

        if ($exception !== null) {
            $this->printException($definition, $exception);

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
     * @param int    $errorNo
     * @param string $errorMessage
     * @param string $errorFile
     * @param int    $errorLine
     * @param array  $errorContext
     * @throws \ErrorException
     */
    public function handleError(
        int $errorNo,
        string $errorMessage,
        string $errorFile,
        int $errorLine,
        array $errorContext
    ) {
        // TODO: Allow to configure the error types that should be transformed to exceptions
        throw $this->createExceptionFromError(
            [
                'message' => $errorMessage,
                'type'    => $errorNo,
                'file'    => $errorFile,
                'line'    => $errorLine,
            ]
        );
    }

    /**
     * @param array $error
     * @param int   $code
     * @return ErrorException
     */
    protected function createExceptionFromError(array $error, $code = 0)
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
     * @param \Throwable          $exception
     */
    private function printException(DefinitionInterface $definition, $exception)
    {
        $this->exceptionPrinter->printException($definition, $exception);
    }

    /**
     * @param DefinitionInterface $definition
     */
    private function printSuccess(DefinitionInterface $definition)
    {
        if ($this->printer->getVerbose()) {
            $this->printer->info('Successfully ran %s', $definition->getDescription());
        } else {
            $this->printer->printf('.');
        }
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
