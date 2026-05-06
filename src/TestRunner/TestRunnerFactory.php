<?php

declare(strict_types=1);

namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\ClassLoader;
use Cundd\TestFlight\Environment;
use Cundd\TestFlight\Event\EventDispatcherInterface;
use Cundd\TestFlight\Exception\ClassException;
use Cundd\TestFlight\ObjectManager;
use Cundd\TestFlight\Output\ExceptionPrinterInterface;
use Cundd\TestFlight\Output\PrinterInterface;

/**
 * Factory for the Test Runners
 */
final class TestRunnerFactory
{
    public function __construct(
        private readonly ClassLoader $classLoader,
        private readonly ObjectManager $objectManager,
        private readonly Environment $environment,
        private readonly PrinterInterface $printer,
        private readonly ExceptionPrinterInterface $exceptionPrinter,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * Return an instance of the given Test Runner class
     *
     * @template T of TestRunnerInterface
     *
     * @param class-string<T> $testRunnerClass
     */
    public function create(string $testRunnerClass): TestRunnerInterface
    {
        $testRunner = $this->objectManager->get(
            $testRunnerClass,
            $this->classLoader,
            $this->objectManager,
            $this->environment,
            $this->printer,
            $this->exceptionPrinter,
            $this->eventDispatcher
        );
        if ($testRunner instanceof TestRunnerInterface) {
            return $testRunner;
        }

        throw new ClassException('Test Runner must implement ' . TestRunnerInterface::class);
    }
}
