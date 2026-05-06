<?php

declare(strict_types=1);

namespace Cundd\TestFlight\FileAnalysis;

/**
 * Interface for File access
 */
interface FileInterface
{
    /**
     * Return the absolute file path
     *
     * @return non-empty-string
     */
    public function getPath(): string;

    /**
     * Return the file name
     */
    public function getName(): string;

    /**
     * Return the file extension
     */
    public function getExtension(): string;

    /**
     * Return the file parent (dirname)
     */
    public function getParent(): string;

    /**
     * Return the file contents
     */
    public function getContents(): string;

    /**
     * Return if the file contents contain the given keyword
     */
    public function containsKeyword(string $keyword): bool;
}
