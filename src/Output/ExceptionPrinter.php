<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24/04/16
 * Time: 17:57
 */

namespace Cundd\TestFlight\Output;


use Cundd\TestFlight\Definition\CodeDefinition;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\MethodDefinition;

/**
 * Special printer for exception output
 *
 * @package Cundd\TestFlight\Output
 */
class ExceptionPrinter extends Printer implements ExceptionPrinterInterface
{
    /**
     * Prints the exception for the given test definition
     *
     * @param DefinitionInterface $definition
     * @param \Throwable          $exception
     * @return $this
     */
    public function printException(DefinitionInterface $definition, $exception)
    {
        $traceAsString = $this->getTraceAsString($definition, $exception);
        $this->printError(
            "Error %s #%s during test %s: \n%s \nin %s\n\n%s",
            get_class($exception),
            $exception->getCode(),
            $this->getTestDescriptionForDefinition($definition),
            $exception->getMessage(),
            $this->getTestLocationForDefinitionAndException($definition, $exception),
            $traceAsString
        );

        return $this;
    }

    /**
     * @param DefinitionInterface $definition
     * @return string
     */
    private function getTestDescriptionForDefinition(
        DefinitionInterface $definition
    ) {
        if ($definition instanceof MethodDefinition) {
            return sprintf(
                '%s%s%s()',
                $definition->getClassName(),
                $definition->getMethodIsStatic() ? '::' : '->',
                $definition->getMethodName()
            );
        } elseif ($definition instanceof CodeDefinition) {
            return $definition->getDescription();
        }

        return '';
    }

    /**
     * @param DefinitionInterface $definition
     * @param \Throwable          $exception
     * @return string
     */
    private function getTraceAsString(
        DefinitionInterface $definition,
        $exception
    ): string
    {
        $stringParts = [];
        $previousStep = [];
        $enableColoredOutput = $this->getEnableColoredOutput();

        $stackTrace = $exception->getTrace();
        $stackTraceCount = count($stackTrace);
        for ($i = 0; $i < $stackTraceCount; $i++) {
            $step = $stackTrace[$i];
            $stepAsString = $this->getTraceStepAsString($step, $i);

            if ($enableColoredOutput && $this->getTraceStepIsTestMethod($definition, $step, $previousStep)) {
                $stringParts[] = self::NORMAL.self::RED_BACKGROUND.$stepAsString.self::RED;
            } else {
                $stringParts[] = $stepAsString;
            }
            $previousStep = $step;
        }

        return implode("\n", $stringParts);
    }

    /**
     * @param array $step
     * @param int   $stepNumber
     * @return string
     */
    private function getTraceStepAsString(array $step, int $stepNumber)
    {
        if (isset($step['class'])) {
            return sprintf(
                '#%d %s%s%s(%s)%s',
                $stepNumber,
                $step['class'],
                $step['type'],
                $step['function'],
                $this->argumentsToString($step['args'] ?? []),
                $this->pathFromStep($step)
            );
        }

        return sprintf(
            '#%d %s(%s)%s',
            $stepNumber,
            $step['function'],
            $this->argumentsToString($step['args'] ?? []),
            $this->pathFromStep($step)
        );
    }

    /**
     * @param array $step
     * @return string
     */
    private function pathFromStep(array $step): string
    {
        if (!isset($step['file'])) {
            return '';
        }

        return sprintf(': %s:%d', $step['file'], $step['line']);
    }

    /**
     * @param array $arguments
     * @return mixed
     */
    private function argumentsToString(array $arguments)
    {
        return implode(
            ', ',
            array_map(
                function ($argument) {
                    if (is_array($argument)) {
                        return '['.$this->argumentsToString($argument).']';
                    }

                    return is_object($argument)
                        ? get_class($argument)
                        : gettype($argument);
                },
                $arguments
            )
        );
    }

    /**
     * @param DefinitionInterface $definition
     * @param array               $step
     * @param array               $previousStep
     * @return bool
     */
    private function getTraceStepIsTestMethod(
        DefinitionInterface $definition,
        array $step,
        array $previousStep
    ): bool
    {
        $filePath = $previousStep['file'] ?? '';

        if ($definition instanceof MethodDefinition) {
            $functionName = $definition->getMethodName();
        } elseif ($definition instanceof CodeDefinition) {
            $functionName = $definition->getRelatedMethodName();
        } else {
            return false;
        }

        if ($filePath === $definition->getFilePath()
            && $step['function'] === $functionName
        ) {
            return true;
        }

        return false;
    }

    /**
     * @test
     */
    protected static function printExceptionTest()
    {
        $prophet = new \Prophecy\Prophet();
        /** @var MethodDefinition $testDefinition */
        /** @var MethodDefinition|object $prophecy */
        $prophecy = $prophet->prophesize(MethodDefinition::class);
        $prophecy->getClassName()->willReturn(__CLASS__);
        $prophecy->getMethodIsStatic()->willReturn(false);
        $prophecy->getMethodName()->willReturn(
            'thisIsTheDummyTestMethodForTheTest'
        );
        $prophecy->getFilePath()->willReturn(__FILE__);
        $testDefinition = $prophecy->reveal();

        $tempOutputStream = fopen('php://memory', 'r+');

        $printer = new static($tempOutputStream, $tempOutputStream);
        $printer->setEnableColoredOutput(false);
        $printer->printException(
            $testDefinition,
            new \Exception('ExceptionMessage')
        );

        rewind($tempOutputStream);
        $output = stream_get_contents($tempOutputStream);

        $testString = 'Error Exception #0 during test '
            .'Cundd\\TestFlight\\Output\\ExceptionPrinter->thisIsTheDummyTestMethodForTheTest(): '
            ."\n"
            .'ExceptionMessage';
        test_flight_assert(
            $testString === substr($output, 0, strlen($testString))
        );
    }

    /**
     * @param DefinitionInterface $definition
     * @param \Throwable          $exception
     * @return string
     */
    private function getTestLocationForDefinitionAndException(DefinitionInterface $definition, $exception): string
    {
        if ($definition instanceof CodeDefinition) {
            return $definition->getFilePath();
        }

        return $exception->getFile().' at '.$exception->getLine();
    }
}
