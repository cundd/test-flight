<?php

declare(strict_types=1);

namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\Definition\DefinitionInterface;

/**
 * Class that invokes the test methods
 */
interface TestRunnerInterface
{
    public const EVENT_TEST_WILL_RUN = 'test.will_run';
    public const EVENT_TEST_DID_RUN = 'test.did_run';

    public function runTestDefinition(DefinitionInterface $definition): bool;
}
