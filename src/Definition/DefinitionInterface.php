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
     * Return the name of the tested class
     *
     * @return class-string
     */
    public function getClassName(): ?string;

    /**
     * Return the file containing the test
     */
    public function getFile(): FileInterface;

    /**
     * Return the path to the file
     */
    public function getFilePath(): string;

    /**
     * Return a description for the output
     */
    public function getDescription(): string;

    /**
     * Return the descriptive type of the definition
     */
    public function getType(): string;
}
