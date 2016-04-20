<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19/04/16
 * Time: 22:16
 */

namespace Cundd\TestFlight;

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

        $locales = [
            LC_CTYPE    => setlocale(LC_CTYPE, 0),
            LC_NUMERIC  => setlocale(LC_NUMERIC, 0),
            LC_TIME     => setlocale(LC_TIME, 0),
            LC_COLLATE  => setlocale(LC_COLLATE, 0),
            LC_MONETARY => setlocale(LC_MONETARY, 0),
            LC_ALL      => setlocale(LC_ALL, 0),
            LC_MESSAGES => setlocale(LC_MESSAGES, 0),
        ];

        $this->environment = new Environment(
            $_ENV,
            date_default_timezone_get(),
            $locales,
            error_reporting(null)
        );
        $this->environment->reset();
        $this->classLoader = new \Cundd\TestFlight\ClassLoader();

        return $this;
    }

    /**
     * Find and run the tests
     */
    public function run(array $arguments)
    {
        $testPath = (count($arguments) > 1) ? $arguments[1] : (__DIR__.'/../src/');

        $fileProvider = new \Cundd\TestFlight\FileAnalysis\FileProvider();

        $classProvider = new \Cundd\TestFlight\FileAnalysis\ClassProvider();
        $classes = $classProvider->findClassesInFiles($fileProvider->findMatchingFiles($testPath));

        $provider = new \Cundd\TestFlight\FileAnalysis\DefinitionProvider($this->classLoader);
        $testDefinitions = $provider->createForClasses($classes);
        $testRunner = new \Cundd\TestFlight\TestRunner($this->classLoader, $this->objectManager);
        $testRunner->runTestDefinitions($testDefinitions);

        return $this;
    }
}
