<?php

declare(strict_types=1);

namespace Cundd\TestFlight\FileAnalysis;

/**
 * Interface for File access
 */
interface FileInterface
{
    /**
     * Returns the absolute file path
     */
    public function getPath(): string;

    /**
     * Returns the file name
     */
    public function getName(): string;

    /**
     * Returns the file extension
     */
    public function getExtension(): string;

    /**
     * Returns the file parent (dirname)
     */
    public function getParent(): string;

    /**
     * Returns the file contents
     */
    public function getContents(): string;

    /**
     * Returns if the file contents contain the given keyword
     */
    public function containsKeyword(string $keyword): bool;
}
