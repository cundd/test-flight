<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28/04/16
 * Time: 21:07
 */

namespace Cundd\TestFlight;


use Cundd\TestFlight\Definition\DocCommentCodeDefinition;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\DocumentationCodeDefinition;
use Cundd\TestFlight\Definition\MethodDefinition;
use Cundd\TestFlight\Definition\StaticMethodDefinition;
use Cundd\TestFlight\Output\ColorInterface;
use Cundd\TestFlight\Output\ExceptionPrinterInterface;
use Cundd\TestFlight\Output\PrinterInterface;
use Cundd\TestFlight\TestRunner\CodeTestRunner;
use Cundd\TestFlight\TestRunner\MethodTestRunner;
use Cundd\TestFlight\TestRunner\StaticMethodTestRunner;
use Cundd\TestFlight\TestRunner\TestRunnerInterface;

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
     * @var int
     */
    private $numberOfTests = 0;

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
     * TestRunner
     *
     * @param ClassLoader               $classLoader
     * @param ObjectManager             $objectManager
     * @param Environment               $environment
     * @param PrinterInterface          $printer
     * @param ExceptionPrinterInterface $exceptionPrinter
     */
    public function __construct(
        ClassLoader $classLoader,
        ObjectManager $objectManager,
        Environment $environment,
        PrinterInterface $printer,
        ExceptionPrinterInterface $exceptionPrinter
    ) {
        $this->classLoader = $classLoader;
        $this->objectManager = $objectManager;
        $this->environment = $environment;
        $this->printer = $printer;
        $this->exceptionPrinter = $exceptionPrinter;
    }

    /**
     * @param DefinitionInterface[] $testCollection
     * @return bool Return if all tests succeeded
     */
    public function runTestDefinitions(array $testCollection)
    {
        $this->printHeader();
        foreach ($testCollection as $className => $definitionCollection) {
            $this->runTestDefinitionsForCollection(
                $definitionCollection,
                $className
            );
        }
        $this->printFooter();

        return 0 === $this->failures;
    }

    /**
     * @param DefinitionInterface[] $definitionCollection
     * @param string                $classOrCollectionName
     * @throws \Exception
     */
    protected function runTestDefinitionsForCollection(
        array $definitionCollection,
        $classOrCollectionName
    ) {
        if (count($definitionCollection) > 0) {
            if (is_string($classOrCollectionName)) {
                $this->printClassInfo($classOrCollectionName);
            }

            foreach ($definitionCollection as $definition) {
                if ($this->runTestDefinition($definition)) {
                    $this->successes += 1;
                } else {
                    $this->failures += 1;
                }

                $this->environment->reset();
            }
        }
    }

    /**
     * @param DefinitionInterface $definition
     * @return bool
     * @throws \Exception
     */
    protected function runTestDefinition(DefinitionInterface $definition)
    {
        switch (true) {
            case $definition instanceof DocCommentCodeDefinition:
                $testRunnerClass = CodeTestRunner::class;
                break;

            case $definition instanceof DocumentationCodeDefinition:
                $testRunnerClass = CodeTestRunner::class;
                break;

            case $definition instanceof StaticMethodDefinition:
                $testRunnerClass = StaticMethodTestRunner::class;
                break;

            case $definition instanceof MethodDefinition:
                $testRunnerClass = MethodTestRunner::class;
                break;

            default:
                throw new \Exception(
                    sprintf(
                        'No test runner found for definition type %s',
                        get_class($definition)
                    )
                );
        }
        $this->numberOfTests += 1;
        $this->printTestInfo($definition);

        /** @var TestRunnerInterface $testRunner */
        $testRunner = $this->objectManager
            ->get(
                $testRunnerClass,
                $this->classLoader,
                $this->objectManager,
                $this->environment,
                $this->printer,
                $this->exceptionPrinter
            );

        return $testRunner->runTestDefinition($definition);
    }

    /**
     * @return PrinterInterface
     */
    private function getPrinter()
    {
        return $this->printer;
    }

    /**
     * Print the test footer output
     */
    private function printFooter()
    {
        $footerContent = sprintf(
            'Tests: %d | Assertions: %d | Successful: %d | Failures: %d',
            $this->numberOfTests,
            Assert::getCount(),
            $this->successes,
            $this->failures
        );

        $color = $this->failures > 0
            ? (ColorInterface::RED_BACKGROUND.ColorInterface::WHITE)
            : (ColorInterface::GREEN_BACKGROUND.ColorInterface::WHITE);
        $printer = $this->getPrinter();
        $printer->println('');
        $printer->println(
            $printer->colorize(
                $color,
                $footerContent
            )
        );
    }

    /**
     * Print the test header output
     */
    private function printHeader()
    {
    }

    /**
     * Print information about the current test class
     *
     * @param string $className
     */
    private function printClassInfo($className)
    {
        $this->getPrinter()->info('Run tests: %s', $className);
    }

    /**
     * Print information about the current test
     *
     * @param DefinitionInterface $definition
     */
    private function printTestInfo(DefinitionInterface $definition)
    {
    }
}
