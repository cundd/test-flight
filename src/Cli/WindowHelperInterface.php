<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Cli;

/**
 * CLI window helper
 */
interface WindowHelperInterface
{
    /**
     * Return if the window supports colors
     */
    public function hasColorSupport(): bool;

    /**
     * Return the window's width
     *
     * @example
     *  assert(is_int((new \Cundd\TestFlight\Cli\WindowHelper)->getWidth()));
     *  assert(0 < (new \Cundd\TestFlight\Cli\WindowHelper)->getWidth());
     */
    public function getWidth(): int;

    /**
     * Return the window's height
     *
     * @example
     *  assert(is_int((new \Cundd\TestFlight\Cli\WindowHelper)->getHeight()));
     *  assert(0 < (new \Cundd\TestFlight\Cli\WindowHelper)->getHeight());
     */
    public function getHeight(): int;
}
