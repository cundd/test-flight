<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Definition;

/**
 * Definition of a test defined as a method
 */
class MethodDefinition extends AbstractMethodDefinition
{
    public function getType(): string
    {
        return 'Method test';
    }
}
