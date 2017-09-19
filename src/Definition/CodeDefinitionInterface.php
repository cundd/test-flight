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
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Returns the pre-processed code
     *
     * @return string
     */
    public function getPreProcessedCode(): string;
}
