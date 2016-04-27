<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24/04/16
 * Time: 17:57
 */

namespace Cundd\TestFlight\Output;


use Cundd\TestFlight\Definition;

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
     * @param Definition $definition
     * @param \Throwable $exception
     * @return $this
     */
    public function printException(Definition $definition, $exception)
    {
        $traceAsString = $this->getTraceAsString($definition, $exception);
        $this->printError(
            "Error %s #%s during test %s%s%s(): \n%s \nin %s at %s\n%s",
            get_class($exception),
            $exception->getCode(),
            $definition->getClassName(),
            $definition->getMethodIsStatic() ? '::' : '->',
            $definition->getMethodName(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $traceAsString
        );

        return $this;
    }

    /**
     * @param Definition $definition
     * @param \Throwable $exception
     * @return string
     */
    private function getTraceAsString(
        Definition $definition,
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

            if ($enableColoredOutput
                && $this->getTraceStepIsTestMethod(
                    $definition,
                    $step,
                    $previousStep
                )
            ) {
                $stringParts[] = self::NORMAL.self::LIGHT_RED.$stepAsString.self::RED;
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
                $this->argumentsToString($step['args']),
                $this->pathFromStep($step)
            );
        }

        return sprintf(
            '#%d %s(%s)%s',
            $stepNumber,
            $step['function'],
            $this->argumentsToString($step['args']),
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
     * @param Definition $definition
     * @param array      $step
     * @param array      $previousStep
     * @return bool
     */
    private function getTraceStepIsTestMethod(
        Definition $definition,
        array $step,
        array $previousStep
    ): bool
    {
        $filePath = $previousStep['file'] ?? '';

        if ($filePath === $definition->getFilePath()
            && $step['function'] === $definition->getMethodName()
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
        /** @var Definition $testDefinition */
        /** @var Definition|object $prophecy */
        $prophecy = $prophet->prophesize(Definition::class);
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
}