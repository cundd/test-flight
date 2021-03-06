<?php
declare(strict_types=1);

namespace Cundd\TestFlight\Definition;

use Cundd\TestFlight\FileAnalysis\FileInterface;


/**
 * Definition of a test
 *
 * @package Cundd\TestFlight
 */
interface DefinitionInterface
{
    /**
     * Returns the name of the tested class
     *
     * @return string
     */
    public function getClassName(): string;

    /**
     * Returns the file containing the test
     *
     * @return FileInterface
     */
    public function getFile(): FileInterface;

    /**
     * Returns the path to the file
     *
     * @return string
     */
    public function getFilePath(): string;

    /**
     * Returns a description for the output
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Returns the descriptive type of the definition
     *
     * @return string
     */
    public function getType(): string;
}