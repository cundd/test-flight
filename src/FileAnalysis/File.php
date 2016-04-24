<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:26
 */

namespace Cundd\TestFlight\FileAnalysis;


use Cundd\TestFlight\Exception\FileNotExistsException;
use Cundd\TestFlight\Exception\FileNotReadableException;

/**
 * File implementation
 */
class File implements FileInterface
{
    /**
     * @var string
     */
    private $path;

    /**
     * File constructor
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        if (!file_exists($this->path)) {
            FileNotExistsException::exceptionForFile($this->path);
        }
        if (!is_readable($this->path)) {
            FileNotReadableException::exceptionForFile($this->path);
        }

        return file_get_contents($this->path);
    }

    /**
     * @param string $keyword
     * @return bool
     */
    public function containsKeyword(string $keyword): bool
    {
        return false !== strpos($this->getContents(), $keyword);
    }
}