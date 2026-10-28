<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Cli;

/**
 * CLI window helper
 */
final class WindowHelper implements WindowHelperInterface
{
    private int $width;

    private int $height;

    /**
     * Return if the window supports colors
     */
    public function hasColorSupport(): bool
    {
        if (PHP_SAPI !== 'cli') {
            return false;
        }

        // Respect the NO_COLOR convention
        if (false !== getenv('NO_COLOR')) {
            return false;
        }

        // Respect dumb terminals
        if ('dumb' === getenv('TERM')) {
            return false;
        }

        // Windows heuristics
        if (DIRECTORY_SEPARATOR === '\\') {
            return false !== getenv('ANSICON')
                || false !== getenv('ConEmuANSI')
                || false !== getenv('WT_SESSION');
        }

        if (function_exists('posix_isatty')) {
            return (bool) @posix_isatty(STDOUT);
        } else {
            return true;
        }
    }

    /**
     * Return the window's width
     *
     * @example
     *  assert(is_int((new \Cundd\TestFlight\Cli\WindowHelper)->getWidth()));
     *  assert(0 < (new \Cundd\TestFlight\Cli\WindowHelper)->getWidth());
     */
    public function getWidth(): int
    {
        if (!isset($this->width)) {
            $this->width = intval($this->systemCall('tput cols'));
        }

        return $this->width;
    }

    /**
     * Return the window's height
     *
     * @example
     *  assert(is_int((new \Cundd\TestFlight\Cli\WindowHelper)->getHeight()));
     *  assert(0 < (new \Cundd\TestFlight\Cli\WindowHelper)->getHeight());
     */
    public function getHeight(): int
    {
        if (!isset($this->height)) {
            $this->height = intval($this->systemCall('tput lines'));
        }

        return $this->height;
    }

    private function systemCall(string $command): string|false
    {
        if (is_callable('exec')) { // @phpstan-ignore function.alreadyNarrowedType
            return exec($command);
        }

        return '';
    }
}
