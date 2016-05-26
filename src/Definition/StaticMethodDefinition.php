<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 07/05/16
 * Time: 11:49
 */
namespace Cundd\TestFlight\Definition;

/**
 * Definition of a test defined as a static method
 */
class StaticMethodDefinition extends AbstractMethodDefinition
{
    /**
     * Returns the descriptive type of the definition
     *
     * @return string
     */
    public function getType(): string
    {
        return 'Static method test';
    }
}
