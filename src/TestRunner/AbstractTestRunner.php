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
use Error;
use ErrorException;
use Exception;
use Throwable;

/**
 * Class that invokes the test methods
 */
abstract class AbstractTestRunner implements TestRunnerInterface
{
    public function __construct(
        protected readonly ClassLoader $classLoader,
        protected readonly ObjectManager $objectManager,
        protected readonly Environment $environment,
        protected readonly PrinterInterface $printer,
        protected readonly ExceptionPrinterInterface $exceptionPrinter,
        protected readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    abstract protected function performTest(
        DefinitionInterface $definition,
        ContextInterface $context,
    ): void;

    public function runTestDefinition(DefinitionInterface $definition): bool
    {
        $context = new Context();
        $this->eventDispatcher->dispatch(
            self::EVENT_TEST_WILL_RUN,
            new Event($definition, $context)
        );
        $this->prepareTestRunnerForDefinition($definition);
        $exception = null;

        set_error_handler($this->handleError(...));
        error_clear_last();
        try {
            $this->performTest($definition, $context);
        } catch (Error $exception) {
        } catch (Exception $exception) {
        }
        $this->environment->reset();
        $this->eventDispatcher->dispatch(
            self::EVENT_TEST_DID_RUN,
            new Event($definition, $context)
        );

        if (null !== $exception) {
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
     * @param array<string,mixed> $errorContext
     *
     * @throws ErrorException
     */
    public function handleError(
        int $errorNo,
        string $errorMessage,
        string $errorFile,
        int $errorLine,
        array $errorContext = [],
    ): never {
        // TODO: Allow to configure the error types that should be transformed
        // to exceptions
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
     * @param array{message:string, type:int, file:string, line:int} $error
     */
    protected function createExceptionFromError(
        array $error,
        int $code = 0,
    ): ErrorException {
        return new ErrorException(
            $error['message'],
            $code,
            $error['type'],
            $error['file'],
            $error['line']
        );
    }

    private function printException(
        DefinitionInterface $definition,
        Throwable $exception,
    ): void {
        $this->exceptionPrinter->printException($definition, $exception);
    }

    private function printSuccess(DefinitionInterface $definition): void
    {
        if ($this->printer->getVerbose()) {
            $this->printer->info('Successfully ran %s', $definition->getDescription());
        } else {
            $this->printer->printf('.');
        }
    }

    private function prepareTestRunnerForDefinition(
        DefinitionInterface $definition,
    ): void {
        if ($definition->getClassName()) {
            $this->classLoader->loadClass(
                $definition->getClassName(),
                $definition->getFile()
            );
        }
    }
}
