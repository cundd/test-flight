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

    /**
     * @var boolean
     */
    private $enableColoredOutput = true;

    /**
     * @var bool
     */
    private $verbose = false;

    /**
     * @var WindowHelper
     */
    private $cliWindowHelper;

    /**
     * Printer constructor.
     *
     * @param resource     $outputStream
     * @param resource     $errorStream
     * @param WindowHelper $cliWindowHelper
     */
    public function __construct($outputStream, $errorStream, WindowHelper $cliWindowHelper)
    {
        $this->outputStream = $outputStream;
        $this->errorStream = $errorStream;
        $this->cliWindowHelper = $cliWindowHelper;
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function printf(string $format, ...$arguments)
    {
        return $this->printWithArray($format, $arguments);
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function println(string $format, ...$arguments)
    {
        return $this->printWithArray($format . PHP_EOL, $arguments);
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function printError(string $format, ...$arguments)
    {
        $format = $this->colorize(self::RED, $format) . PHP_EOL;
        fwrite($this->errorStream, vsprintf($format, $arguments));

        return $this;
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function debug(string $format, ...$arguments)
    {
        if ($this->getVerbose()) {
            $this->printWithArray($format . PHP_EOL, $arguments);
        }

        return $this;
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function info(string $format, ...$arguments)
    {
        if ($this->getVerbose()) {
            $this->printWithArray($this->colorize(self::BLUE, $format) . PHP_EOL, $arguments);
        }

        return $this;
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function warn(string $format, ...$arguments)
    {
        if ($this->getVerbose()) {
            $this->printWithArray($this->colorize(self::YELLOW, $format) . PHP_EOL, $arguments);
        }

        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setVerbose(bool $flag = false)
    {
        $this->verbose = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function getVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * Returns if colors should be enabled
     *
     * @return boolean
     */
    public function getEnableColoredOutput(): bool
    {
        return $this->enableColoredOutput && $this->cliWindowHelper->hasColorSupport();
    }

    /**
     * Set if colored output should be enabled
     *
     * @param boolean $enableColoredOutput
     * @return PrinterInterface
     */
    public function setEnableColoredOutput(bool $enableColoredOutput): PrinterInterface
    {
        $this->enableColoredOutput = $enableColoredOutput;

        return $this;
    }

    /**
     * Wrap the text in colors
     *
     * @param string $startColor
     * @param string $text
     * @param string $endColor
     * @return string
     */
    public function colorize(string $startColor, string $text, string $endColor = self::NORMAL): string
    {
        if ($this->getEnableColoredOutput()) {
            return $startColor . $text . $endColor;
        }

        return $text;
    }

    /**
     * @param string $format
     * @param array  $arguments
     * @return $this
     */
    private function printWithArray($format, array $arguments)
    {
        fwrite($this->outputStream, vsprintf($format, $arguments));

        return $this;
    }
}
