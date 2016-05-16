<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 07/05/16
 * Time: 13:05
 */
namespace Cundd\TestFlight\DefinitionProvider;

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
     * @return array|\Cundd\TestFlight\Definition\DefinitionInterface[]
     */
    public function createForClasses(array $classNameToFiles);

    /**
     * Create the test definitions for the given documentation files
     *
     * @param FileInterface[] $files
     * @return array|\Cundd\TestFlight\Definition\DefinitionInterface[]
     */
    public function createForDocumentation(array $files);
}
