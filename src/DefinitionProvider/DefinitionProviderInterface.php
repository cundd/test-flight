<?php
declare(strict_types=1);

namespace Cundd\TestFlight\DefinitionProvider;

use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * Provider for test definitions of classes containing test methods
 */
interface DefinitionProviderInterface
{
    /**
     * Set the types of tests to run
     *
     * @param string[] $types
     * @return $this
     */
    public function setTypes(array $types);

    /**
     * Create the test definitions for the given classes
     *
     * @param array $classNameToFiles
     * @return DefinitionInterface[][]
     */
    public function createForClasses(array $classNameToFiles): array;

    /**
     * Create the test definitions for the given documentation files
     *
     * @param FileInterface[] $files
     * @return DefinitionInterface[][]
     */
    public function createForDocumentation(array $files): array;
}
