<?php
declare(strict_types=1);

namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\Definition\DefinitionInterface;


/**
 * Class that invokes the test methods
 */
interface TestRunnerInterface
{
    const EVENT_TEST_WILL_RUN = 'test.will_run';
    const EVENT_TEST_DID_RUN = 'test.did_run';

    /**
     * @param DefinitionInterface $definition
     * @return bool
     */
    public function runTestDefinition(DefinitionInterface $definition): bool;
}