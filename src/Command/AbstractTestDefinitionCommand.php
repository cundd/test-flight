<?php
declare(strict_types=1);


namespace Cundd\TestFlight\Command;


use Cundd\TestFlight\CodeExtractor;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\DefinitionProvider\DefinitionProvider;
use Cundd\TestFlight\DefinitionProvider\DefinitionProviderInterface;
use Cundd\TestFlight\FileAnalysis\ClassProvider;
use Cundd\TestFlight\FileAnalysis\DocumentationFileProvider;
use Cundd\TestFlight\FileAnalysis\FileInterface;
use Cundd\TestFlight\FileAnalysis\FileProvider;

/**
 * Abstract class for commands that can collect test definitions
 *
 * @package Cundd\TestFlight\Command
 */
abstract class AbstractTestDefinitionCommand extends AbstractCommand
{
    /**
     * @return DefinitionInterface[][]
     */
    protected function collectTestDefinitions()
    {
        $testPath = $this->configurationProvider->get('path');

        /** @var FileProvider $fileProvider */
        $fileProvider = $this->objectManager->get(FileProvider::class);
        $allFiles = $fileProvider->findMatchingFiles($testPath);
        $codeExtractor = $this->objectManager->get(CodeExtractor::class);

        /** @var \Cundd\TestFlight\DefinitionProvider\DefinitionProviderInterface $provider */
        $provider = $this->objectManager->get(DefinitionProvider::class, $this->classLoader, $codeExtractor);
        if (0 !== count($this->configurationProvider->get('types'))) {
            $provider->setTypes($this->configurationProvider->get('types'));
        }

        return array_merge(
            $this->collectTestDefinitionsForClasses($provider, $allFiles),
            $this->collectTestDefinitionsForDocumentation($provider, $allFiles)
        );
    }

    /**
     * @param DefinitionProviderInterface $provider
     * @param FileInterface[]             $allFiles
     * @return DefinitionInterface[][]
     */
    private function collectTestDefinitionsForDocumentation(DefinitionProviderInterface $provider, array $allFiles)
    {
        /** @var DocumentationFileProvider $documentationFileProvider */
        $documentationFileProvider = $this->objectManager->get(DocumentationFileProvider::class);
        $documentationFiles = $documentationFileProvider->findDocumentationFiles($allFiles);

        return $provider->createForDocumentation($documentationFiles);
    }

    /**
     * @param DefinitionProviderInterface $provider
     * @param FileInterface[]             $allFiles
     * @return DefinitionInterface[][]
     */
    private function collectTestDefinitionsForClasses(DefinitionProviderInterface $provider, array $allFiles)
    {
        $classProvider = $this->objectManager->get(ClassProvider::class);
        $classes = $classProvider->findClassesInFiles($allFiles);

        return $provider->createForClasses($classes);
    }
}
