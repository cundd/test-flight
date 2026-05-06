<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Command;

/**
 * Interface for a CLI Command
 */
interface CommandInterface
{
    /**
     * Run the command
     */
    public function run(): bool;
}
