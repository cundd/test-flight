<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19/05/16
 * Time: 20:17
 */

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
class TestRunnerFactory
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
     * @var PrinterInterface
     */
    private $printer;

    /**
     * @var ExceptionPrinterInterface
     */
    private $exceptionPrinter;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

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
     * Returns an instance of the given Test Runner class
     *
     * @param string $testRunnerClass
     * @return TestRunnerInterface
     */
    public function create($testRunnerClass)
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