<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24/04/16
 * Time: 17:30
 */
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
    public function getPath() : string;

    /**
     * Returns the file contents
     *
     * @return string
     */
    public function getContents() : string;

    /**
     * Returns if the file contents contain the given keyword
     *
     * @param string $keyword
     * @return bool
     */
    public function containsKeyword(string $keyword) : bool;
}
