<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Command;

use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\Output\PrinterInterface;

/**
 * Command to list all tests
 */
class ListCommand extends AbstractTestDefinitionCommand
{
    public function run(): bool
    {
        foreach ($this->collectTestDefinitions() as $key => $testDefinitionCollection) {
            if (count($testDefinitionCollection) > 0) {
                $this->printer->println(
                    $this->printer->colorize(
                        PrinterInterface::CYAN_BACKGROUND . PrinterInterface::WHITE,
                        $key
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
}
