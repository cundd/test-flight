<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 19/04/16
 * Time: 22:16
 */

declare(strict_types = 1);

namespace Cundd\TestFlight;

use Cundd\TestFlight\Autoload\Finder;
use Cundd\TestFlight\Cli\OptionParser;
use Cundd\TestFlight\Cli\WindowHelper;
use Cundd\TestFlight\Configuration\ConfigurationProviderInterface;
use Cundd\TestFlight\Configuration\Exception\ConfigurationException;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\DefinitionProvider\DefinitionProviderInterface;
use Cundd\TestFlight\Event\EventDispatcherInterface;
use Cundd\TestFlight\Exception\FileNotExistsException;
use Cundd\TestFlight\FileAnalysis\ClassProvider;
use Cundd\TestFlight\DefinitionProvider\DefinitionProvider;
use Cundd\TestFlight\FileAnalysis\DocumentationFileProvider;
use Cundd\TestFlight\FileAnalysis\FileInterface;
use Cundd\TestFlight\FileAnalysis\FileProvider;
use Cundd\TestFlight\Output\CodeFormatter;
use Cundd\TestFlight\Output\ExceptionPrinterInterface;
use Cundd\TestFlight\Output\PrinterInterface;
use Cundd\TestFlight\TestRunner\TestRunnerFactory;

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
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ConfigurationProviderInterface
     */
    private $configurationProvider;

    /**
     * @var PrinterInterface
     */
    private $printer;

    /**
     * @var ExceptionPrinterInterface
     */
    private $exceptionPrinter;

    /**
     * @return $this
     */
    public function init()
    {
        $this->objectManager = new ObjectManager();
        $this->configurationProvider = $this->objectManager->get(ConfigurationProviderInterface::class);
        $this->classLoader = $this->objectManager->get(ClassLoader::class);

        // Prepare the printers
        $windowHelper = $this->objectManager->get(WindowHelper::class);
        $codeFormatter = $this->objectManager->get(CodeFormatter::class, $windowHelper);
        $this->printer = $this->objectManager->get(
            PrinterInterface::class,
            STDOUT,
            STDERR,
            $windowHelper
        );
        $this->exceptionPrinter = $this->objectManager->get(
            ExceptionPrinterInterface::class,
            STDOUT,
            STDERR,
            $windowHelper,
            $codeFormatter
        );
        $this->eventDispatcher = $this->objectManager->get(EventDispatcherInterface::class);

        $this->checkDependencies();
        $this->initEnvironment();
        $this->printHeader();

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
        $exception = null;
        try {
            $this->prepareConfigurationProvider($arguments);
            $this->prepareCustomBootstrapAndAutoloading($this->configurationProvider->get('bootstrap'));
            $this->printer->setVerbose($this->configurationProvider->get('verbose'));
            $this->exceptionPrinter->setVerbose($this->configurationProvider->get('verbose'));

            $testDefinitions = $this->collectTestDefinitions();

            /** @var TestRunnerFactory $testRunnerFactory */
            $testRunnerFactory = $this->objectManager->get(
                TestRunnerFactory::class,
                $this->classLoader,
                $this->objectManager,
                $this->environment,
                $this->printer,
                $this->exceptionPrinter,
                $this->eventDispatcher
            );

            /** @var TestDispatcher $testDispatcher */
            $testDispatcher = $this->objectManager->get(
                TestDispatcher::class,
                $testRunnerFactory,
                $this->printer
            );

            $result = $testDispatcher->runTestDefinitions($testDefinitions);
            $this->printFooter();

            return $result;
        } catch (ConfigurationException $exception) {
        } catch (FileNotExistsException $exception) {
        }
        
        $this->error($exception->getMessage());

        return null;
    }

    /**
     * @return Definition\DefinitionInterface[]
     */
    private function collectTestDefinitions()
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
                date_default_timezone_get(),
                $locales,
                error_reporting(null),
                $GLOBALS,
                $_ENV,
                $_GET,
                $_POST,
                $_COOKIE,
                $_SERVER,
                $_SESSION,
                $_FILES,
                $_REQUEST
            );
            $this->environment->reset();
        } catch (\TypeError $error) {
            echo $error;
        }
    }

    /**
     * @param string[] $arguments
     * @throws \Exception
     */
    private function prepareConfigurationProvider(array $arguments)
    {
        $options = $this->objectManager->get(OptionParser::class)->parse($arguments);

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

        if (isset($options['bootstrap'])) {
            // Bootstrap is properly set
        } elseif (isset($options['classloader'])) {
            $options['bootstrap'] = $options['classloader'];
        } else {
            $options['bootstrap'] = '';
        }

        // Check for a configuration file
        if (!isset($options['configuration'])) {
            $localConfigurationFilePath = getcwd().'/'.ConfigurationProviderInterface::LOCAL_CONFIGURATION_FILE_NAME;
            if (file_exists($localConfigurationFilePath)) {
                $options['configuration'] = $localConfigurationFilePath;
            }
        }

        $this->configurationProvider->setConfiguration($options);

        if (!$this->configurationProvider->get('path')) {
            // TODO: Read the composer.json and scan the sources?
            $this->error('Please specify a path to look for tests');
        }
    }

    /**
     * Print the test header output
     */
    private function printHeader()
    {
        $this->printer->println('Test-Flight %s', Constants::VERSION);
    }

    /**
     * Print the test footer output
     */
    private function printFooter()
    {
        // Nothing to do at the moment
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

    /**
     *
     */
    private function checkDependencies()
    {
        if (php_sapi_name() !== 'cli') {
            $this->error('Test-Flight must be run as CLI');
        }
        if (!class_exists('RecursiveDirectoryIterator')) {
            // This must not happen
            $this->error('SPL must be enabled');
        }
        if (!is_callable('token_get_all')) {
            $this->error('Tokenizer must be enabled and callable (http://php.net/manual/en/book.tokenizer.php)');
        }
    }

    /**
     * Include the custom bootstrap file
     *
     * @param string $bootstrapFile
     */
    private function prepareCustomBootstrapAndAutoloading(string $bootstrapFile)
    {
        /** @var Finder $autoloadFinder */
        $autoloadFinder = $this->objectManager->get(Finder::class);
        $projectAutoloaderPath = $autoloadFinder->find(getcwd());
        if ($projectAutoloaderPath !== '') {
            require_once $projectAutoloaderPath;
        }

        // The variable will be exported to the bootstrap file
        $eventDispatcher = $this->eventDispatcher;
        if ($bootstrapFile) {
            require_once $bootstrapFile;
        }
        unset($eventDispatcher);
    }
}
