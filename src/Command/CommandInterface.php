<?php
declare(strict_types=1);

namespace Cundd\TestFlight\Command;

/**
 * Interface for a CLI Command
 */
interface CommandInterface
{
    /**
     * Runs the command
     *
     * @return bool
     */
    public function run(): bool;
}
