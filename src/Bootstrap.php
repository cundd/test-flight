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
use Exception;
use TypeError;

/**
 * Bootstrap the test environment
 *
 * @phpstan-import-type Configuration from ConfigurationProviderInterface
 */
final class Bootstrap
{
    private ObjectManager $objectManager;

    private ClassLoader $classLoader;

    private EventDispatcherInterface $eventDispatcher;

    private ConfigurationProviderInterface $configurationProvider;

    private PrinterInterface $printer;

    private ExceptionPrinterInterface $exceptionPrinter;

    public function init(): self
    {
        $this->objectManager = new ObjectManager();
        $this->configurationProvider = $this->objectManager->get(
            ConfigurationProviderInterface::class
        );
        $this->classLoader = $this->objectManager->get(ClassLoader::class);

        // Prepare the printers
        $windowHelper = $this->objectManager->get(WindowHelper::class);
        $codeFormatter = $this->objectManager->get(
            CodeFormatter::class,
            $windowHelper
        );
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
        $this->eventDispatcher = $this->objectManager->get(
            EventDispatcherInterface::class
        );
        $environment = $this->objectManager->get(Environment::class);

        $this->checkDependencies();

        $this->initEnvironment($environment);
        $this->printHeader();

        return $this;
    }

    /**
     * Find and run the tests
     *
     * @param array<int,mixed> $arguments
     */
    public function run(
        array $arguments,
    ): bool {
        $exception = null;
        try {
            $this->prepareConfigurationProvider(
                $arguments
            );
            $this->prepareCustomBootstrapAndAutoloading(
                $this->configurationProvider->get('bootstrap')
            );
            $this->printer->setVerbose(
                $this->configurationProvider->get('verbose')
            );
            $this->exceptionPrinter->setVerbose(
                $this->configurationProvider->get('verbose')
            );

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
    }

    private function initEnvironment(Environment $environment): void
    {
        try {
            $locales = [
                LC_CTYPE    => setlocale(LC_CTYPE, '0'),
                LC_NUMERIC  => setlocale(LC_NUMERIC, '0'),
                LC_TIME     => setlocale(LC_TIME, '0'),
                LC_COLLATE  => setlocale(LC_COLLATE, '0'),
                LC_MONETARY => setlocale(LC_MONETARY, '0'),
                LC_ALL      => setlocale(LC_ALL, '0'),
                LC_MESSAGES => setlocale(LC_MESSAGES, '0'),
            ];

            $environment->store(
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
            $environment->reset();
        } catch (TypeError $error) {
            echo $error;
        }
    }

    /**
     * @param string[] $arguments
     *
     * @throws Exception
     */
    private function prepareConfigurationProvider(array $arguments): void
    {
        $options = $this->objectManager
            ->get(OptionParser::class)
            ->parse($arguments);

        /** @var Configuration $configuration */
        $configuration = $options;
        $configuration['types'] = $this->prepareTypes($options);

        $configuration['verbose'] = (bool) (
            $options['verbose']
            ?? $options['v']
            ?? false
        );

        if (isset($options['bootstrap'])) {
            if (!is_string($options['bootstrap'])) {
                $this->error(sprintf(
                    'If argument "bootstrap" is given, it must be of type "string", %s given',
                    get_debug_type($options['bootstrap'])
                ));
            }
            $configuration['bootstrap'] = (string) $options['bootstrap'];
        } elseif (isset($options['classloader'])) {
            if (!is_string($options['classloader'])) {
                $this->error(sprintf(
                    'If argument "classloader" is given, it must be of type "string", %s given',
                    get_debug_type($options['classloader'])
                ));
            }
            $configuration['bootstrap'] = (string) $options['classloader'];
        } else {
            $configuration['bootstrap'] = '';
        }

        if (isset($options['list']) && $options['list']) {
            $configuration['command'] = 'list';
        } elseif (is_string($options['command'] ?? null)) {
            $configuration['command'] = (string) $options['command'];
        } else {
            $configuration['command'] = 'run';
        }

        // Check for a configuration file
        if (is_string($options['configuration'] ?? null)) {
            $configuration['configuration'] = $options['configuration'];
        } else {
            $localConfigurationFilePath = getcwd()
                . '/'
                . ConfigurationProviderInterface::LOCAL_CONFIGURATION_FILE_NAME;
            if (file_exists($localConfigurationFilePath)) {
                $configuration['configuration'] = $localConfigurationFilePath;
            }
        }

        $this->configurationProvider->setConfiguration($configuration);

        if (!$this->configurationProvider->get('paths')) {
            // TODO: Read the composer.json and scan the sources?
            $this->error('Please specify a path to look for tests');
        }
    }

    /**
     * @param array<string,string|string[]|bool> $options
     *
     * @return array<int<0,max>, string>
     */
    private function prepareTypes(array $options): array
    {
        $rawTypes = $options['types'] ??
        $options['type'] ?? null;
        if (null === $rawTypes) {
            return [];
        }

        if (is_array($rawTypes)) {
            return array_map(function (mixed $rawType) {
                if (!is_scalar($rawType)) {
                    $this->error(sprintf(
                        'Types must be strings, %s given',
                        get_debug_type($rawType)
                    ));
                }

                return (string) $rawType;
            }, $rawTypes);
        }

        if (is_bool($rawTypes)) {
            return [];
        }

        return array_filter(explode(
            ',',
            $rawTypes
        ));
    }

    /**
     * Print the test header output
     */
    private function printHeader(): void
    {
        $this->printer->println('Test-Flight %s', Constants::VERSION);
    }

    /**
     * Print the test footer output
     */
    private function printFooter(): void
    {
        // Nothing to do at the moment
    }

    /**
     * Print a error message and exit
     *
     * @param int $status
     */
    private function error(string $message, $status = 1): never
    {
        $this->printer->printError($message);
        exit($status);
    }

    private function checkDependencies(): void
    {
        if ('cli' !== php_sapi_name()) {
            $this->error('Test-Flight must be run as CLI');
        }
        if (!class_exists('RecursiveDirectoryIterator')) {
            // This must not happen
            $this->error('SPL must be enabled');
        }
        if (!is_callable('token_get_all')) { // @phpstan-ignore function.alreadyNarrowedType
            $this->error('Tokenizer must be enabled and callable (http://php.net/manual/en/book.tokenizer.php)');
        }
    }

    /**
     * Include the custom bootstrap file
     *
     * @param non-empty-string $bootstrapFile
     */
    private function prepareCustomBootstrapAndAutoloading(
        string $bootstrapFile,
    ): void {
        /** @var Finder $autoloadFinder */
        $autoloadFinder = $this->objectManager->get(Finder::class);
        $projectAutoloaderPath = $autoloadFinder->find(getcwd() ?: __DIR__);
        if ('' !== $projectAutoloaderPath) {
            require_once $projectAutoloaderPath;
        }

        // The variable will be exported to the bootstrap file
        $eventDispatcher = $this->eventDispatcher;
        if ($bootstrapFile) {
            $bootstrapCallback = require_once $bootstrapFile;
            if (is_callable($bootstrapCallback)) {
                $bootstrapCallback($eventDispatcher);
            }
        }
        unset($eventDispatcher);
    }
}
