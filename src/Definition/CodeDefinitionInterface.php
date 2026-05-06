<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Definition;

/**
 * Interface for Code based definitions
 */
interface CodeDefinitionInterface extends DefinitionInterface
{
    /**
     * Return the code
     */
    public function getCode(): string;

    /**
     * Return the pre-processed code
     */
    public function getPreProcessedCode(): string;
}
