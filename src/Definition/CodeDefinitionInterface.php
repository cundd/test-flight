<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Definition;

/**
 * Interface for Code based definitions
 */
interface CodeDefinitionInterface extends DefinitionInterface
{
    /**
     * Returns the code
     */
    public function getCode(): string;

    /**
     * Returns the pre-processed code
     */
    public function getPreProcessedCode(): string;
}
