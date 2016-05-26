<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 26/05/16
 * Time: 13:07
 */

namespace Cundd\TestFlight\Command;

use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Output\PrinterInterface;

/**
 * Command to list all tests
 */
class ListCommand extends AbstractTestDefinitionCommand
{
    /**
     * Runs the command
     *
     * @return bool
     */
    public function run(): bool
    {
        foreach ($this->collectTestDefinitions() as $key => $testDefinitionCollection) {
            if (count($testDefinitionCollection) > 0) {
                $this->printer->println(
                    $this->printer->colorize(
                        PrinterInterface::CYAN_BACKGROUND.PrinterInterface::WHITE,
                        $this->printGroupHeaderForKeyAndDefinition($key, reset($testDefinitionCollection))
                    )
                );
                /** @var DefinitionInterface $testDefinition */
                foreach ($testDefinitionCollection as $testDefinition) {
                    $this->printer->println($testDefinition->getDescription());
                }
            }
        }

        return true;
    }

    /**
     * @param string              $key
     * @param DefinitionInterface $definition
     * @return string
     */
    private function printGroupHeaderForKeyAndDefinition(string $key, DefinitionInterface $definition): string
    {
        return $key;
    }
}
