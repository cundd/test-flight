<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 22:34
 */

namespace Cundd\TestFlight;

use Cundd\TestFlight\FileAnalysis\File;

class TestDefinition
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $method;

    /**
     * @var File
     */
    private $file;

    /**
     * @var bool
     */
    private $_isStatic;

    /**
     * TestDefinition constructor.
     *
     * @param string $className
     * @param string $method
     * @param File   $file
     * @param bool   $isStatic
     */
    public function __construct($className, $method, File $file, $isStatic)
    {
        $this->className = $className;
        $this->method    = $method;
        $this->file      = $file;
        $this->_isStatic = (bool)$isStatic;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return boolean
     */
    public function isStatic()
    {
        return $this->_isStatic;
    }
}
