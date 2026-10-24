<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Configuration;

use Cundd\TestFlight\Configuration\Exception\InvalidConfigurationException;
use Cundd\TestFlight\Configuration\Exception\InvalidFileTypeException;
use Cundd\TestFlight\Configuration\Exception\InvalidJsonException;
use Cundd\TestFlight\FileAnalysis\File;
use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * @phpstan-import-type Configuration from ConfigurationProviderInterface
 */
class ConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * @var Configuration
     */
    private array $configuration;

    /**
     * @param Configuration $coreConfiguration The core configuration (provided through CLI options)
     */
    public function __construct(array $coreConfiguration = [])
    {
        $this->setConfiguration($coreConfiguration);
    }

    public function setConfiguration(
        array $configuration,
    ): ConfigurationProviderInterface {
        if (!empty($configuration['configuration'])) {
            $configuration = array_merge(
                $configuration,
                $this->load($configuration['configuration']),
                array_filter($configuration)
            );
        }

        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Return the configuration for the given key
     *
     * <code>
     *  $cp = new \Cundd\TestFlight\Configuration\ConfigurationProvider(
     *      ['configuration' => __DIR__ . '/../../tests/resources/test-configuration.json']
     *  );
     *  test_flight_assert_same('/tests/resources/test-bootstrap.php', substr($cp->get('bootstrap'), -35));
     *
     *  $cp = new \Cundd\TestFlight\Configuration\ConfigurationProvider();
     *  $cp->setConfiguration(['configuration' => __DIR__ . '/../../tests/resources/test-configuration.json']);
     *  test_flight_assert_same('/tests/resources/test-bootstrap.php', substr($cp->get('bootstrap'), -35));
     * </code>
     */
    public function get(string $key): mixed
    {
        return $this->configuration[$key] ?? null;
    }

    /**
     * @param non-empty-string $path
     *
     * @return array<string,mixed>
     */
    private function load(string $path): array
    {
        $file = new File($path);

        if ('json' !== $file->getExtension()) {
            throw new InvalidFileTypeException(
                sprintf(
                    'File type %s not supported (currently only JSON files are supported)',
                    $file->getExtension()
                ),
                1463827284
            );
        }

        $data = json_decode($this->prepareJsonContent($file), true);
        if (json_last_error() > 0) {
            throw new InvalidJsonException(
                sprintf(
                    'Invalid JSON configuration in file %s: %s',
                    $file->getPath(),
                    json_last_error_msg()
                ),
                1463827638
            );
        }
        if (!is_array($data)) {
            throw new InvalidConfigurationException(
                sprintf(
                    'JSON configuration data must be an array in file %s',
                    $file->getPath()
                ),
                1463827639
            );
        }

        $this->preparePathInConfiguration($data, 'bootstrap', $file);
        $this->preparePathInConfiguration($data, 'path', $file);

        return $data;
    }

    /**
     * @param array<string,mixed> $configuration
     */
    private function preparePathInConfiguration(
        array &$configuration,
        string $key,
        FileInterface $file,
    ): void {
        if (!isset($configuration[$key])) {
            return;
        }

        $path = $configuration[$key];
        if (!$path) {
            return;
        }
        if (!is_string($path)) {
            throw new InvalidConfigurationException(sprintf(
                'Path must be of type "string", "%s" given',
                get_debug_type($path)
            ));
        }
        if ('/' !== $path[0]) {
            $path = $file->getParent() . '/' . $path;
        }

        $configuration[$key] = realpath($path) ?: $path;
    }

    private function prepareJsonContent(FileInterface $file): string
    {
        return (string) preg_replace('!//.*!', '', $file->getContents());
    }

    /**
     * @test
     */
    protected function createTest(): void
    {
        test_flight_throws(
            fn () => new self([
                'configuration' => __DIR__ . '/../../tests/resources/test-configuration-invalid-json.json',
            ]),
            InvalidJsonException::class
        );
        test_flight_throws(
            fn () => new self([
                'configuration' => __DIR__ . '/../../tests/resources/test-configuration-invalid-data.json',
            ]),
            InvalidConfigurationException::class
        );
    }
}
