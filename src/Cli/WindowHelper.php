<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Cli;

/**
 * CLI window helper
 */
class WindowHelper
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * Returns if the window supports colors
     *
     * @return bool
     */
    public function hasColorSupport()
    {
        if (isset($_SERVER['TERM'])) {
            return in_array($_SERVER['TERM'], ['xterm-256color']);
        }

        return false;
    }

    /**
     * Returns the window's width
     *
     * @example
     *  assert(is_int((new \Cundd\TestFlight\Cli\WindowHelper)->getWidth()));
     *  assert(0 < (new \Cundd\TestFlight\Cli\WindowHelper)->getWidth());
     *
     * @return int
     */
    public function getWidth()
    {
        if (null === $this->width) {
            $this->width = intval($this->systemCall('tput cols'));
        }

        return $this->width;
    }

    /**
     * Returns the window's height
     *
     * @example
     *  assert(is_int((new \Cundd\TestFlight\Cli\WindowHelper)->getHeight()));
     *  assert(0 < (new \Cundd\TestFlight\Cli\WindowHelper)->getHeight());
     *
     * @return int
     */
    public function getHeight()
    {
        if (null === $this->height) {
            $this->height = intval($this->systemCall('tput lines'));
        }

        return $this->height;
    }

    /**
     * @return string
     */
    private function systemCall(string $command)
    {
        if (is_callable('exec')) {
            return exec($command);
        }

        return '';
    }
}
