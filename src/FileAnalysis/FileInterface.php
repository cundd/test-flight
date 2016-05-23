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
    public function getPath();

    /**
     * Returns the file name
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the file extension
     *
     * @return string
     */
    public function getExtension();

    /**
     * Returns the file parent (dirname)
     *
     * @return string
     */
    public function getParent();

    /**
     * Returns the file contents
     *
     * @return string
     */
    public function getContents();

    /**
     * Returns if the file contents contain the given keyword
     *
     * @param string $keyword
     * @return bool
     */
    public function containsKeyword($keyword);
}
