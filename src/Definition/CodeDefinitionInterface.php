<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 07/05/16
 * Time: 13:53
 */
namespace Cundd\TestFlight\Definition;


/**
 * Interface for Code based definitions
 */
interface CodeDefinitionInterface extends DefinitionInterface
{
    /**
     * Returns the code
     * 
     * @return string
     */
    public function getCode() : string;
}
