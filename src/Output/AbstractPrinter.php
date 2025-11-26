<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Output;

use Cundd\TestFlight\Cli\WindowHelper;

/**
 * Abstract printer base class
 */
abstract class AbstractPrinter implements PrinterInterface
{
    /**
     * @var resource
     */
    private $outputStream;

    /**
     * @var resource
     */
    private $errorStream;

    private bool $enableColoredOutput = true;

    private bool $verbose = false;

    private WindowHelper $cliWindowHelper;

    /**
     * @param resource $outputStream
     * @param resource $errorStream
     */
    public function __construct(
        $outputStream,
        $errorStream,
        WindowHelper $cliWindowHelper,
    ) {
        $this->outputStream = $outputStream;
        $this->errorStream = $errorStream;
        $this->cliWindowHelper = $cliWindowHelper;
    }

    /**
     * @param array ...$arguments
     */
    public function printf(string $format, ...$arguments): self
    {
        return $this->printWithArray($format, $arguments);
    }

    /**
     * @param array ...$arguments
     */
    public function println(string $format, ...$arguments): self
    {
        return $this->printWithArray($format . PHP_EOL, $arguments);
    }

    /**
     * @param array ...$arguments
     */
    public function printError(string $format, ...$arguments): self
    {
        $format = $this->colorize(self::RED, $format) . PHP_EOL;
        fwrite($this->errorStream, vsprintf($format, $arguments));

        return $this;
    }

    /**
     * @param array ...$arguments
     */
    public function debug(string $format, ...$arguments): self
    {
        if ($this->getVerbose()) {
            $this->printWithArray($format . PHP_EOL, $arguments);
        }

        return $this;
    }

    /**
     * @param array ...$arguments
     */
    public function info(string $format, ...$arguments): self
    {
        if ($this->getVerbose()) {
            $this->printWithArray($this->colorize(self::BLUE, $format) . PHP_EOL, $arguments);
        }

        return $this;
    }

    /**
     * @param array ...$arguments
     */
    public function warn(string $format, ...$arguments): self
    {
        if ($this->getVerbose()) {
            $this->printWithArray($this->colorize(self::YELLOW, $format) . PHP_EOL, $arguments);
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

    private function printWithArray(string $format, array $arguments): self
    {
        fwrite($this->outputStream, vsprintf($format, $arguments));

        return $this;
    }
}
