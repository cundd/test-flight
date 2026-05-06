<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Output;

use Cundd\TestFlight\Cli\WindowHelperInterface;
use Cundd\TestFlight\Definition\AbstractMethodDefinition;
use Cundd\TestFlight\Definition\CodeDefinitionInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Definition\DocCommentCodeDefinition;
use Cundd\TestFlight\Definition\MethodDefinition;
use Cundd\TestFlight\Exception\AssertionError;
use Cundd\TestFlight\TestRunner\AbstractTestRunner;
use Exception;
use RuntimeException;
use Throwable;

/**
 * Special printer for exception output
 *
 * @phpstan-type Frame array{function:string,line?:int,file?:string,class?:string,type?:string}
 */
final class ExceptionPrinter extends AbstractPrinter implements ExceptionPrinterInterface
{
    /**
     * Create a new printer instance
     *
     * @param resource $outputStream
     * @param resource $errorStream
     */
    public function __construct(
        $outputStream,
        $errorStream,
        WindowHelperInterface $cliWindowHelper,
        private readonly CodeFormatter $codeFormatter,
    ) {
        parent::__construct($outputStream, $errorStream, $cliWindowHelper);
    }

    public function printException(
        DefinitionInterface $definition,
        Throwable $exception,
    ): self {
        $traceAsString = $this->getTraceAsString($definition, $exception);
        $this->printError(
            "\nError %s #%s during test %s \n%s \nin %s\n\n%s",
            get_class($exception),
            $exception->getCode(),
            $this->getTestDescriptionForDefinition($definition),
            $exception->getMessage(),
            $this->getTestLocationForDefinitionAndException($definition, $exception),
            $traceAsString
        );

        return $this;
    }

    private function getTestDescriptionForDefinition(
        DefinitionInterface $definition,
    ): string {
        if ($definition instanceof AbstractMethodDefinition) {
            return sprintf(
                '%s%s%s():',
                $definition->getClassName(),
                $definition->getMethodIsStatic() ? '::' : '->',
                $definition->getMethodName()
            );
        } elseif ($definition instanceof CodeDefinitionInterface) {
            $description = $definition->getDescription();
            if ($this->getVerbose()) {
                $description .= ":\nCode:\n"
                    . $this->codeFormatter->formatCode(
                        $definition->getPreProcessedCode(),
                        $this->getEnableColoredOutput()
                    )
                    . "\n";
            }

            return $description;
        }

        return '';
    }

    private function getTraceAsString(
        DefinitionInterface $definition,
        Throwable $exception,
    ): string {
        $stringParts = [];
        $previousStep = null;
        $enableColoredOutput = $this->getEnableColoredOutput();

        $stackTrace = $exception->getTrace();
        $stackTraceCount = count($stackTrace);
        for ($i = 0; $i < $stackTraceCount; ++$i) {
            $frame = $stackTrace[$i];

            if ($this->shouldStopTraceAtFrame($frame)) {
                break;
            }

            $stepAsString = $this->getTraceFrameAsString($frame, $i);
            $isTestMethod = $this->getTraceStepIsTestMethod(
                $definition,
                $frame,
                $previousStep
            );
            if ($enableColoredOutput && $isTestMethod) {
                $stringParts[] = self::NORMAL . self::RED_BACKGROUND . $stepAsString . self::RED;
            } else {
                $stringParts[] = $stepAsString;
            }
            $previousStep = $frame;
        }

        return implode("\n", $stringParts);
    }

    /**
     * Stop printing the stack trace at \Cundd\TestFlight\TestRunner\AbstractTestRunner->runTestDefinition()
     *
     * @param Frame $frame
     */
    private function shouldStopTraceAtFrame(array $frame): bool
    {
        return isset($frame['class'])
            && AbstractTestRunner::class === $frame['class']
            && 'runTestDefinition' === $frame['function'];
    }

    /**
     * @param Frame $frame
     */
    private function getTraceFrameAsString(array $frame, int $stepNumber): string
    {
        if (isset($frame['class'])) {
            return sprintf(
                '#%d %s%s%s(%s)%s',
                $stepNumber,
                $frame['class'],
                $frame['type'] ?? '',
                $frame['function'],
                $this->argumentsToString($frame['args'] ?? []),
                $this->pathFromFrame($frame)
            );
        }

        return sprintf(
            '#%d %s(%s)%s',
            $stepNumber,
            $frame['function'],
            $this->argumentsToString($frame['args'] ?? []),
            $this->pathFromFrame($frame)
        );
    }

    /**
     * @param Frame $frame
     */
    private function pathFromFrame(array $frame): string
    {
        if (!isset($frame['file']) || !isset($frame['line'])) {
            return '';
        }

        return sprintf(': %s:%d', $frame['file'], $frame['line']);
    }

    /**
     * @param array<mixed> $arguments
     */
    private function argumentsToString(array $arguments): string
    {
        return implode(
            ', ',
            array_map(
                function ($argument) {
                    if (is_array($argument)) {
                        return '[' . $this->argumentsToString($argument) . ']';
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
     * @param Frame  $frame
     * @param ?Frame $previousFrame
     */
    private function getTraceStepIsTestMethod(
        DefinitionInterface $definition,
        array $frame,
        ?array $previousFrame,
    ): bool {
        $filePath = $previousFrame['file'] ?? '';

        if ($definition instanceof AbstractMethodDefinition) {
            $functionName = $definition->getMethodName();
        } elseif ($definition instanceof DocCommentCodeDefinition) {
            $functionName = $definition->getRelatedMethodName();
        } else {
            return false;
        }

        return (bool) (
            $filePath === $definition->getFilePath()
            && $frame['function'] === $functionName
        )

        ;
    }

    private function getTestLocationForDefinitionAndException(
        DefinitionInterface $definition,
        Throwable $exception,
    ): string {
        if ($exception instanceof AssertionError) {
            return $definition->getFilePath();
        }

        return $exception->getFile() . ' at ' . $exception->getLine();
    }

    /**
     * @test
     */
    protected static function printExceptionTest(): void
    {
        $prophet = new \Prophecy\Prophet();

        /** @var WindowHelperInterface $windowHelper */
        $windowHelper = $prophet->prophesize(WindowHelperInterface::class)->reveal();

        /** @var CodeFormatter $codeFormatter */
        $codeFormatter = $prophet->prophesize(CodeFormatter::class)->reveal();

        /** @var \Prophecy\Prophecy\ObjectProphecy<MethodDefinition> $prophecy */
        $prophecy = $prophet->prophesize(MethodDefinition::class);
        $prophecy->getClassName()->willReturn(__CLASS__);
        $prophecy->getMethodIsStatic()->willReturn(false);
        $prophecy->getMethodName()->willReturn(
            'thisIsTheDummyTestMethodForTheTest'
        );
        $prophecy->getFilePath()->willReturn(__FILE__);
        $testDefinition = $prophecy->reveal();

        $tempOutputStream = fopen('php://memory', 'r+');
        if (false === $tempOutputStream) {
            throw new RuntimeException('Could not open temporary output stream');
        }

        $printer = new static(
            $tempOutputStream,
            $tempOutputStream,
            $windowHelper,
            $codeFormatter
        );
        $printer->setEnableColoredOutput(false);
        $printer->printException(
            $testDefinition,
            new Exception('ExceptionMessage')
        );

        rewind($tempOutputStream);
        $output = stream_get_contents($tempOutputStream);

        $testString = "\n"
            . 'Error Exception #0 during test '
            . 'Cundd\\TestFlight\\Output\\ExceptionPrinter->thisIsTheDummyTestMethodForTheTest(): '
            . "\n"
            . 'ExceptionMessage';
        test_flight_assert_same(
            $testString,
            substr($output, 0, strlen($testString))
        );
    }
}
