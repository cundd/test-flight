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
use Cundd\TestFlight\Exception\InvalidArgumentException;

class File
{
    /**
     * @var string
     */
    private $path;

    /**
     * File constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->assertStringType($path, 'path');
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getContents()
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
    public function containsKeyword($keyword)
    {
        $this->assertStringType($keyword, 'keyword');

        return false !== strpos($this->getContents(), $keyword);
    }

    /**
     * @param mixed  $input
     * @param string $argumentName
     */
    private function assertStringType($input, $argumentName)
    {
        if (!is_string($input)) {
            throw InvalidArgumentException::exceptionForVariableAndExpectedTypes(
                $argumentName,
                ['string'],
                $input
            );
        }
    }
}