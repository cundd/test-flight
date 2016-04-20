<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19/04/16
 * Time: 22:16
 */

namespace Cundd\TestFlight;

use Cundd\TestFlight\FileAnalysis\ClassProvider;
use Cundd\TestFlight\FileAnalysis\DefinitionProvider;
use Cundd\TestFlight\FileAnalysis\FileProvider;

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

        $this->initEnvironment();

        $this->classLoader = $this->objectManager->get(ClassLoader::class);

        return $this;
    }

    /**
     * Find and run the tests
     */
    public function run(array $arguments)
    {
        $testDefinitions = $this->collectTestDefinitions($arguments);
        $testRunner = $this->objectManager->get(TestRunner::class, $this->classLoader, $this->objectManager);
        $testRunner->runTestDefinitions($testDefinitions);

        return $this;
    }

    /**
     * @param array $arguments
     * @return Definition[]
     */
    private function collectTestDefinitions(array $arguments)
    {
        $testPath = (count($arguments) > 1) ? $arguments[1] : (__DIR__.'/../src/');

        $fileProvider = $this->objectManager->get(FileProvider::class);
        $classProvider = $this->objectManager->get(ClassProvider::class);
        $classes = $classProvider->findClassesInFiles($fileProvider->findMatchingFiles($testPath));

        $provider = $this->objectManager->get(DefinitionProvider::class, $this->classLoader);

        $testDefinitions = $provider->createForClasses($classes);

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
}
