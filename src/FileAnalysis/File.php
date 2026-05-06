<?php

declare(strict_types=1);

namespace Cundd\TestFlight\FileAnalysis;

use Cundd\TestFlight\Exception\FileNotExistsException;
use Cundd\TestFlight\Exception\FileNotReadableException;

/**
 * File implementation
 */
class File implements FileInterface
{
    private string $contents;

    /**
     * @param non-empty-string $path
     */
    public function __construct(private readonly string $path)
    {
    }

    /**
     * Return the file's path
     *
     * @example
     *  $file = new \Cundd\TestFlight\FileAnalysis\File(__FILE__);
     *  assert(__FILE__ === $file->getPath())
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function getName(): string
    {
        return basename($this->path);
    }

    /**
     * Returns the file extension
     */
    public function getExtension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Returns the file parent (dirname)
     */
    public function getParent(): string
    {
        return dirname($this->path);
    }

    public function getContents(): string
    {
        if (!isset($this->contents)) {
            if (!file_exists($this->path)) {
                FileNotExistsException::exceptionForFile($this->path);
            }
            if (!is_readable($this->path)) {
                FileNotReadableException::exceptionForFile($this->path);
            }

            $this->contents = (string) file_get_contents($this->path);
        }

        return $this->contents;
    }

    public function containsKeyword(string $keyword): bool
    {
        return false !== strpos($this->getContents(), $keyword);
    }
}
