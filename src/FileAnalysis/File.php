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
     * @var string
     */
    private $contents;

    /**
     * File constructor
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Returns the file's path
     *
     * @example
     *  $file = new \Cundd\TestFlight\FileAnalysis\File(__FILE__);
     *  assert(__FILE__ === $file->getPath())
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the file name
     *
     * @return string
     */
    public function getName() 
    {
        return basename($this->path);
    }

    /**
     * Returns the file extension
     *
     * @return string
     */
    public function getExtension()
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    /**
     * Returns the file parent (dirname)
     *
     * @return string
     */
    public function getParent()
    {
        return dirname($this->path);
    }

    /**
     * @return string
     */
    public function getContents()
    {
        if (!$this->contents) {
            if (!file_exists($this->path)) {
                FileNotExistsException::exceptionForFile($this->path);
            }
            if (!is_readable($this->path)) {
                FileNotReadableException::exceptionForFile($this->path);
            }

            $this->contents = file_get_contents($this->path);
        }

        return $this->contents;
    }

    /**
     * @param string $keyword
     * @return bool
     */
    public function containsKeyword($keyword)
    {
        return false !== strpos($this->getContents(), $keyword);
    }
}
