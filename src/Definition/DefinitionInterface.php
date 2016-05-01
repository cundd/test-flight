<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 27/04/16
 * Time: 20:46
 */
namespace Cundd\TestFlight\Definition;

use Cundd\TestFlight\FileAnalysis\FileInterface;


/**
 * Definition of a test
 *
 * @package Cundd\TestFlight
 */
interface DefinitionInterface
{
    /**
     * Returns the name of the tested class
     *
     * @return string
     */
    public function getClassName() : string;

    /**
     * Returns the file containing the test
     *
     * @return FileInterface
     */
    public function getFile() : FileInterface;

    /**
     * Returns the path to the file
     *
     * @return string
     */
    public function getFilePath() : string;

    /**
     * Returns a description for the output
     * 
     * @return string
     */
    public function getDescription(): string;
}