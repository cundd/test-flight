<?php


declare(strict_types=1);

namespace Cundd\TestFlight;

use Cundd\TestFlight\Autoload\Finder;
use Cundd\TestFlight\Cli\OptionParser;
use Cundd\TestFlight\Cli\WindowHelper;
use Cundd\TestFlight\Command\CommandInterface;
use Cundd\TestFlight\Command\ListCommand;
use Cundd\TestFlight\Command\RunCommand;
use Cundd\TestFlight\Configuration\ConfigurationProviderInterface;
use Cundd\TestFlight\Configuration\Exception\ConfigurationException;
use Cundd\TestFlight\Event\EventDispatcherInterface;
use Cundd\TestFlight\Exception\FileNotExistsException;
use Cundd\TestFlight\Output\CodeFormatter;
use Cundd\TestFlight\Output\ExceptionPrinterInterface;
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
    public function run(array $arguments): bool
    {
        $exception = null;
        try {
            $this->prepareConfigurationProvider($arguments);
            $this->prepareCustomBootstrapAndAutoloading($this->configurationProvider->get('bootstrap'));
            $this->printer->setVerbose($this->configurationProvider->get('verbose'));
            $this->exceptionPrinter->setVerbose($this->configurationProvider->get('verbose'));

            switch ($this->configurationProvider->get('command')) {
                case 'list':
                    $commandClass = ListCommand::class;
                    break;

                case 'run':
                default:
                    $commandClass = RunCommand::class;
                    break;
            }

            /** @var CommandInterface $command */
            $command = $this->objectManager->get(
                $commandClass,
                $this->configurationProvider,
                $this->objectManager,
                $this->classLoader,
                $this->printer,
                $this->exceptionPrinter
            );
            $result = $command->run();

            $this->printFooter();

            return $result;
        } catch (ConfigurationException $exception) {
        } catch (FileNotExistsException $exception) {
        }

        $this->error($exception->getMessage());

        return false;
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
                $_SESSION ?? [],
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

        if (isset($options['list']) && $options['list']) {
            $options['command'] = 'list';
        } elseif (!isset($options['command'])) {
            $options['command'] = 'run';
        }

        // Check for a configuration file
        if (!isset($options['configuration'])) {
            $localConfigurationFilePath = getcwd(
                ) . '/' . ConfigurationProviderInterface::LOCAL_CONFIGURATION_FILE_NAME;
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
