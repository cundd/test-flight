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
use Cundd\TestFlight\Environment;
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
        $this->printer = $printer;
        $this->exceptionPrinter = $exceptionPrinter;
        $this->environment = $environment;
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
    public function runTestDefinition(DefinitionInterface $definition)
    {
        $this->prepareTestRunnerForDefinition($definition);
        $exception = null;

        self::error_clear_last();
        try {
            $this->performTest($definition);
        } catch (\Error $exception) {
        } catch (\Exception $exception) {
        }
        $this->environment->reset();

        if ($exception !== null) {
            $this->printException($definition, $exception);

            return false;
        }

        $lastError = self::error_get_last();
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
    
    /**
     * Clear the most recent error.
     * 
     * @see http://php.net/error_clear_last
     */
    private static function error_clear_last()
    {
        if (function_exists("error_clear_last")) {
            error_clear_last();
            return;
        }
        
        // See http://php.net/manual/de/function.error-get-last.php#113518
        set_error_handler('var_dump', 0);
        @$eMuMA2fS;
        restore_error_handler();
    }
    
    /**
     * Gets the last error.
     * 
     * @see http://php.net/error_get_last
     */
    private static function error_get_last()
    {
        $lastError = error_get_last();
        
        if (function_exists("error_clear_last")) {
            return $lastError;
        }
        
        if (!empty($lastError) && strpos($lastError["message"], "eMuMA2fS") !== false) {
            return null;
        }
        return $lastError;
    }
}
