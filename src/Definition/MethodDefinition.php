<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 22:34
 */
declare(strict_types = 1);

namespace Cundd\TestFlight\Definition;

/**
 * Definition of a test defined as a method
 */
class MethodDefinition extends AbstractMethodDefinition
{
    /**
     * Returns the descriptive type of the definition
     *
     * @return string
     */
    public function getType(): string
    {
        return 'Method test';
    }
}
