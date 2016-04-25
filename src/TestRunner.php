<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 20:46
 */

namespace Cundd\TestFlight;

use Cundd\TestFlight\Output\ExceptionPrinterInterface;
use Cundd\TestFlight\Output\Printer;
use Cundd\TestFlight\Output\PrinterInterface;
use ErrorException;

/**
 * Class that invokes the test methods
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
     * TestRunner
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
     * @return bool Return if all tests succeeded
     */
    public function runTestDefinitions(array $testCollection): bool
    {
        $this->printHeader();
        foreach ($testCollection as $className => $definitionCollection) {
            $this->runTestDefinitionsForClass($className, $definitionCollection);
        }
        $this->printFooter();

        return 0 === $this->failures;
    }

    /**
     * @param string       $className
     * @param Definition[] $definitionCollection
     */
    private function runTestDefinitionsForClass(string $className, array $definitionCollection)
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
    private function runTestDefinition(Definition $definition): bool
    {
        $this->classLoader->loadClass($definition->getClassName(), $definition->getFile());

        error_clear_last();
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
        $className = $definition->getClassName();
        $methodName = $definition->getMethodName();

        if ($definition->getMethodIsPublic()) {
            call_user_func([$className, $methodName]);
        } else {
            $method = new \ReflectionMethod($className, $methodName);
            $method->setAccessible(true);
            $method->invoke(null);
        }
    }

    /**
     * @param Definition $definition
     * @return bool
     */
    private function runInstanceTest(Definition $definition)
    {
        $instance = $this->objectManager->create($definition->getClassName());
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
        $this->getExceptionPrinter()->printException($definition, $exception);
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
        return $this->objectManager->get(PrinterInterface::class, STDOUT, STDERR);
    }

    /**
     * @return ExceptionPrinterInterface
     */
    private function getExceptionPrinter()
    {
        return $this->objectManager->get(ExceptionPrinterInterface::class, STDOUT, STDERR);
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
