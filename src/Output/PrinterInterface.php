<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Output;

interface PrinterInterface extends ColorInterface
{
    public function printf(string $format, mixed ...$arguments): self;

    public function println(string $format, mixed ...$arguments): self;

    public function printError(string $format, mixed ...$arguments): self;

    public function info(string $format, mixed ...$arguments): self;

    public function debug(string $format, mixed ...$arguments): self;

    public function warn(string $format, mixed ...$arguments): self;

    /**
     * Return if colors should be enabled
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

    public function setVerbose(bool $flag = false): self;

    public function getVerbose(): bool;
}
