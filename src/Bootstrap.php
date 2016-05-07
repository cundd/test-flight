<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19/04/16
 * Time: 22:16
 */

declare(strict_types = 1);

namespace Cundd\TestFlight;

use Cundd\TestFlight\Cli\OptionParser;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\DefinitionProvider\DefinitionProviderInterface;
use Cundd\TestFlight\FileAnalysis\ClassProvider;
use Cundd\TestFlight\DefinitionProvider\DefinitionProvider;
use Cundd\TestFlight\FileAnalysis\DocumentationFileProvider;
use Cundd\TestFlight\FileAnalysis\FileInterface;
use Cundd\TestFlight\FileAnalysis\FileProvider;
use Cundd\TestFlight\Output\PrinterInterface;

/**
 * Bootstrap the test environment
 */
class Bootstrap
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var Environment
     */
    private $environment;

    /**
     * @var ClassLoader
     */
    private $classLoader;

    /**
     * @var PrinterInterface
     */
    private $printer;

    /**
     * @return $this
     */
    public function init()
    {
        $this->objectManager = new ObjectManager();
        $this->classLoader = $this->objectManager->get(ClassLoader::class);

        // Prepare the printer
        $this->printer = $this->objectManager->get(
            PrinterInterface::class,
            STDOUT,
            STDERR
        );

        $this->initEnvironment();

        return $this;
    }

    /**
     * Find and run the tests
     *
     * @param array $arguments
     * @return bool
     */
    public function run(array $arguments)
    {
        $options = $this->prepareArguments($arguments);

        $this->printer->setVerbose($options['verbose']);

        $testDefinitions = $this->collectTestDefinitions($options);
        /** @var TestDispatcher $testRunner */
        $testRunner = $this->objectManager->get(TestDispatcher::class, $this->classLoader, $this->objectManager);

        return $testRunner->runTestDefinitions($testDefinitions);
    }

    /**
     * @param array $options
     * @return Definition\DefinitionInterface[]
     */
    private function collectTestDefinitions(array $options)
    {
        $testPath = $options['path'];

        /** @var FileProvider $fileProvider */
        $fileProvider = $this->objectManager->get(FileProvider::class);

        $allFiles = $fileProvider->findMatchingFiles($testPath);
        $codeExtractor = $this->objectManager->get(CodeExtractor::class);

        /** @var \Cundd\TestFlight\DefinitionProvider\DefinitionProviderInterface $provider */
        $provider = $this->objectManager->get(DefinitionProvider::class, $this->classLoader, $codeExtractor);
        $provider->setTypes($options['types']);

        return array_merge(
            $this->collectTestDefinitionsForClasses($provider, $allFiles),
            $this->collectTestDefinitionsForDocumentation($provider, $allFiles)
        );
    }

//    public function fffTest()
//    {
//        $codeExtractor = new \Cundd\TestFlight\CodeExtractor();
//
//        $testPath = __DIR__;
//        $fileProvider = new \Cundd\TestFlight\FileAnalysis\FileProvider();
//        $classProvider = new \Cundd\TestFlight\FileAnalysis\ClassProvider();
//        $classes = $classProvider->findClassesInFiles($fileProvider->findMatchingFiles($testPath));
//
//        assert(is_array($classes));
//    }

    /**
     *
     */
    private function initEnvironment()
    {
        try {
            $locales = [
                LC_CTYPE    => setlocale(LC_CTYPE, 0),
                LC_NUMERIC  => setlocale(LC_NUMERIC, 0),
                LC_TIME     => setlocale(LC_TIME, 0),
                LC_COLLATE  => setlocale(LC_COLLATE, 0),
                LC_MONETARY => setlocale(LC_MONETARY, 0),
                LC_ALL      => setlocale(LC_ALL, 0),
                LC_MESSAGES => setlocale(LC_MESSAGES, 0),
            ];

            $this->environment = $this->objectManager->get(Environment::class);
            $this->environment->store(
                $_ENV,
                date_default_timezone_get(),
                $locales,
                error_reporting(null),
                $GLOBALS
            );
            $this->environment->reset();
        } catch (\TypeError $error) {
            echo $error;
        }
    }

    /**
     * @param string[] $arguments
     * @return array
     * @throws \Exception
     */
    private function prepareArguments(array $arguments)
    {
        $options = $this->objectManager->get(OptionParser::class)->parse($arguments);

        if (!isset($options['path'])) {
            // TODO: Read the composer.json and scan the sources?
            $this->error('Please specify a path to look for tests');
        }
        if (isset($options['types'])) {
            $options['types'] = explode(',', $options['types']);
        } elseif (isset($options['type'])) {
            $options['types'] = explode(',', $options['type']);
        } else {
            $options['types'] = [];
        }

        if (isset($options['verbose'])) {
            $options['verbose'] = boolval($options['verbose']);
        } elseif (isset($options['v'])) {
            $options['verbose'] = true;
        } else {
            $options['verbose'] = false;
        }

        return $options;
    }

    /**
     * Print a error message and exit
     *
     * @param string $message
     * @param int    $status
     */
    private function error(string $message, $status = 1)
    {
        $this->printer->printError($message);
        exit($status);
    }

    /**
     * @param DefinitionProviderInterface $provider
     * @param FileInterface[]             $allFiles
     * @return DefinitionInterface[]
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
     * @return DefinitionInterface[]
     */
    private function collectTestDefinitionsForClasses(DefinitionProviderInterface $provider, array $allFiles)
    {
        $classProvider = $this->objectManager->get(ClassProvider::class);
        $classes = $classProvider->findClassesInFiles($allFiles);

        return $provider->createForClasses($classes);
    }
}
