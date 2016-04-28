<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28/04/16
 * Time: 21:07
 */

namespace Cundd\TestFlight;


use Cundd\TestFlight\Definition\CodeDefinition;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\MethodDefinition;
use Cundd\TestFlight\Output\PrinterInterface;
use Cundd\TestFlight\TestRunner\CodeTestRunner;
use Cundd\TestFlight\TestRunner\MethodTestRunner;
use Cundd\TestFlight\TestRunner\StaticMethodTestRunner;

/**
 * Class to forward the test definitions to the matching test runner
 */
class TestDispatcher
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
     * @var int
     */
    private $successes = 0;

    /**
     * @var int
     */
    private $failures = 0;

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
     * @param DefinitionInterface[] $testCollection
     * @return bool Return if all tests succeeded
     */
    public function runTestDefinitions(array $testCollection): bool
    {
        $this->printHeader();
        foreach ($testCollection as $className => $definitionCollection) {
            $this->runTestDefinitionsForClass(
                $className,
                $definitionCollection
            );
        }
        $this->printFooter();

        return 0 === $this->failures;
    }

    /**
     * @param string                $className
     * @param DefinitionInterface[] $definitionCollection
     */
    protected function runTestDefinitionsForClass(
        string $className,
        array $definitionCollection
    ) {
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
     * @param DefinitionInterface $definition
     * @return bool
     * @throws \Exception
     */
    protected function runTestDefinition(DefinitionInterface $definition): bool
    {
        if ($definition instanceof CodeDefinition) {
            $testRunnerClass = CodeTestRunner::class;
        } elseif ($definition instanceof MethodDefinition) {
            if ($definition->getMethodIsStatic()) {
                $testRunnerClass = StaticMethodTestRunner::class;
            } else {
                $testRunnerClass = MethodTestRunner::class;
            }
        } else {
            throw new \Exception(
                sprintf(
                    'No test runner found for definition type %s',
                    get_class($definition)
                )
            );
        }

        return $this->objectManager
            ->get($testRunnerClass, $this->classLoader, $this->objectManager)
            ->runTestDefinition($definition);
    }

    /**
     * @return PrinterInterface
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
     * Print the test footer output
     */
    private function printFooter()
    {
        $this->getPrinter()->println(
            '%d Assertions | Successful: %d | Failures: %d',
            Assert::getCount(),
            $this->successes,
            $this->failures
        );
    }

    /**
     * Print the test header output
     */
    private function printHeader()
    {
        $this->getPrinter()->println('Test-Flight %s', Constants::VERSION);
    }
}