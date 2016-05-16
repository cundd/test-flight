<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28/04/16
 * Time: 21:12
 */
namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\Definition\DefinitionInterface;


/**
 * Class that invokes the test methods
 */
interface TestRunnerInterface
{
    /**
     * @param DefinitionInterface $definition
     * @return bool
     */
    public function runTestDefinition(DefinitionInterface $definition);
}