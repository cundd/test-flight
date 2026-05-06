<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Output;

use Cundd\TestFlight\Cli\WindowHelperInterface;

/**
 * Abstract printer base class
 */
abstract class AbstractPrinter implements PrinterInterface
{
    private bool $enableColoredOutput = true;

    private bool $verbose = false;

    /**
     * @param resource $outputStream
     * @param resource $errorStream
     */
    public function __construct(
        private readonly mixed $outputStream,
        private readonly mixed $errorStream,
        private readonly WindowHelperInterface $cliWindowHelper,
    ) {
    }

    public function printf(string $format, mixed ...$arguments): self
    {
        return $this->printWithArray($format, $arguments);
    }

    public function println(string $format, mixed ...$arguments): self
    {
        return $this->printWithArray($format . PHP_EOL, $arguments);
    }

    public function printError(string $format, mixed ...$arguments): self
    {
        $format = $this->colorize(self::RED, $format) . PHP_EOL;
        fwrite($this->errorStream, vsprintf($format, $arguments));

        return $this;
    }

    public function debug(string $format, mixed ...$arguments): self
    {
        if ($this->getVerbose()) {
            $this->printWithArray($format . PHP_EOL, $arguments);
        }

        return $this;
    }

    public function info(string $format, mixed ...$arguments): self
    {
        if ($this->getVerbose()) {
            $this->printWithArray(
                $this->colorize(self::BLUE, $format) . PHP_EOL,
                $arguments
            );
        }

        return $this;
    }

    public function warn(string $format, mixed ...$arguments): self
    {
        if ($this->getVerbose()) {
            $this->printWithArray(
                $this->colorize(self::YELLOW, $format) . PHP_EOL,
                $arguments
            );
        }

        return $this;
    }

    public function setVerbose(bool $flag = false): self
    {
        $this->verbose = $flag;

        return $this;
    }

    public function getVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * Returns if colors should be enabled
     */
    public function getEnableColoredOutput(): bool
    {
        return $this->enableColoredOutput && $this->cliWindowHelper->hasColorSupport();
    }

    /**
     * Set if colored output should be enabled
     */
    public function setEnableColoredOutput(
        bool $enableColoredOutput,
    ): PrinterInterface {
        $this->enableColoredOutput = $enableColoredOutput;

        return $this;
    }

    /**
     * Wrap the text in colors
     */
    public function colorize(
        string $startColor,
        string $text,
        string $endColor = self::NORMAL,
    ): string {
        if ($this->getEnableColoredOutput()) {
            return $startColor . $text . $endColor;
        }

        return $text;
    }

    /**
     * @param array<int|string,mixed> $arguments
     */
    private function printWithArray(string $format, array $arguments): self
    {
        fwrite($this->outputStream, vsprintf($format, $arguments));

        return $this;
    }
}
