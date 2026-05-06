<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Definition;

/**
 * Definition of a test defined as a static method
 */
class StaticMethodDefinition extends AbstractMethodDefinition
{
    public function getType(): string
    {
        return 'Static method test';
    }
}
