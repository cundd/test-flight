<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Definition;

use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * Definition of a test
 */
interface DefinitionInterface
{
    /**
     * Returns the name of the tested class
     */
    public function getClassName(): string;

    /**
     * Returns the file containing the test
     */
    public function getFile(): FileInterface;

    /**
     * Returns the path to the file
     */
    public function getFilePath(): string;

    /**
     * Returns a description for the output
     */
    public function getDescription(): string;

    /**
     * Returns the descriptive type of the definition
     */
    public function getType(): string;
}
