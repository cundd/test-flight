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
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Returns the file name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Returns the file extension
     *
     * @return string
     */
    public function getExtension(): string;

    /**
     * Returns the file parent (dirname)
     *
     * @return string
     */
    public function getParent(): string;

    /**
     * Returns the file contents
     *
     * @return string
     */
    public function getContents(): string;

    /**
     * Returns if the file contents contain the given keyword
     *
     * @param string $keyword
     * @return bool
     */
    public function containsKeyword(string $keyword): bool;
}
