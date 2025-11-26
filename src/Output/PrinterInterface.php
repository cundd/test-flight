<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Output;

interface PrinterInterface extends ColorInterface
{
    /**
     * @param array ...$arguments
     */
    public function printf(string $format, ...$arguments): self;

    /**
     * @param array ...$arguments
     */
    public function println(string $format, ...$arguments): self;

    /**
     * @param array ...$arguments
     */
    public function printError(string $format, ...$arguments): self;

    /**
     * @param array ...$arguments
     */
    public function info(string $format, ...$arguments): self;

    /**
     * @param array ...$arguments
     */
    public function warn(string $format, ...$arguments): self;

    /**
     * @param array ...$arguments
     */
    public function debug(string $format, ...$arguments): self;

    /**
     * Returns if colors should be enabled
     */
    public function getEnableColoredOutput(): bool;

    /**
     * Set if colored output should be enabled
     */
    public function setEnableColoredOutput(bool $enableColoredOutput): self;

    /**
     * Wrap the text in colors
     */
    public function colorize(
        string $startColor,
        string $text,
        string $endColor = self::NORMAL,
    ): string;

    /**
     * @return $this
     */
    public function setVerbose(bool $flag = false);

    public function getVerbose(): bool;
}
