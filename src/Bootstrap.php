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
use Cundd\TestFlight\FileAnalysis\ClassProvider;
use Cundd\TestFlight\FileAnalysis\DefinitionProvider;
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
     * @return $this
     */
    public function init()
    {
        $this->objectManager = new ObjectManager();
        $this->classLoader = $this->objectManager->get(ClassLoader::class);

        // Prepare the printer
        $this->objectManager->get(
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
        $testDefinitions = $this->collectTestDefinitions($options);
        /** @var TestDispatcher $testRunner */
        $testRunner = $this->objectManager->get(TestDispatcher::class, $this->classLoader, $this->objectManager);

        return $testRunner->runTestDefinitions($testDefinitions);
    }

    /**
     * @param array $options
     * @return Definition\MethodDefinition[]
     */
    private function collectTestDefinitions(array $options)
    {
        $testPath = $options['path'];
        $types = $options['types'];

        $codeExtractor = $this->objectManager->get(CodeExtractor::class);
        $fileProvider = $this->objectManager->get(FileProvider::class);
        $classProvider = $this->objectManager->get(ClassProvider::class);
        $classes = $classProvider->findClassesInFiles($fileProvider->findMatchingFiles($testPath));

        /** @var DefinitionProvider $provider */
        $provider = $this->objectManager->get(DefinitionProvider::class, $this->classLoader, $codeExtractor);

        $testDefinitions = $provider->createForClasses($classes, $types);

        return $testDefinitions;
    }

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
                error_reporting(null)
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
            $options['path'] = __DIR__.'/../src/';
        }
        if (isset($options['types'])) {
            $options['types'] = explode(',', $options['types']);
        } elseif (isset($options['type'])) {
            $options['types'] = explode(',', $options['type']);
        } else {
            $options['types'] = [];
        }

        return $options;
    }
}
