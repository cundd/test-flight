<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 26/05/16
 * Time: 13:07
 */

namespace Cundd\TestFlight\Command;

use Cundd\TestFlight\Environment;
use Cundd\TestFlight\Event\EventDispatcherInterface;
use Cundd\TestFlight\TestDispatcher;
use Cundd\TestFlight\TestRunner\TestRunnerFactory;

/**
 * Command that runs all tests
 */
class RunCommand extends AbstractTestDefinitionCommand
{
    /**
     * Runs the command
     *
     * @return bool
     */
    public function run(): bool
    {
        $testDefinitions = $this->collectTestDefinitions();

        /** @var TestRunnerFactory $testRunnerFactory */
        $testRunnerFactory = $this->objectManager->get(
            TestRunnerFactory::class,
            $this->classLoader,
            $this->objectManager,
            $this->objectManager->get(Environment::class),
            $this->printer,
            $this->exceptionPrinter,
            $this->objectManager->get(EventDispatcherInterface::class)
        );

        /** @var TestDispatcher $testDispatcher */
        $testDispatcher = $this->objectManager->get(
            TestDispatcher::class,
            $testRunnerFactory,
            $this->printer
        );

        return $testDispatcher->runTestDefinitions($testDefinitions);
    }
}
