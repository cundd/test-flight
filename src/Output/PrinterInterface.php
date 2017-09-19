<?php
declare(strict_types=1);


namespace Cundd\TestFlight\Output;

interface PrinterInterface extends ColorInterface
{
    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function printf(string $format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function println(string $format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function printError(string $format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function info(string $format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function warn(string $format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function debug(string $format, ...$arguments);

    /**
     * Returns if colors should be enabled
     *
     * @return boolean
     */
    public function getEnableColoredOutput(): bool;

    /**
     * Set if colored output should be enabled
     *
     * @param boolean $enableColoredOutput
     * @return PrinterInterface
     */
    public function setEnableColoredOutput(bool $enableColoredOutput): PrinterInterface;

    /**
     * Wrap the text in colors
     *
     * @param string $startColor
     * @param string $text
     * @param string $endColor
     * @return string
     */
    public function colorize(string $startColor, string $text, string $endColor = self::NORMAL): string;

    /**
     * @param bool $flag
     * @return $this
     */
    public function setVerbose(bool $flag = false);

    /**
     * @return bool
     */
    public function getVerbose(): bool;
}
