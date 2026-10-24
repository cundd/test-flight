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

        if (isset($data['bootstrap'])) {
            if (!is_string($data['bootstrap'])) {
                throw new InvalidConfigurationException(sprintf(
                    'Bootstrap file path must be of type "string", "%s" given',
                    get_debug_type($data['bootstrap'])
                ));
            }

            $data['bootstrap'] = $this->preparePath($data['bootstrap'], $file);
        }

        $data['paths'] = $this->preparePaths(
            $data['paths'] ?? $data['path'] ?? [],
            $file
        );
        unset($data['path']);

        return $data;
    }

    /**
     * @param string[]|string $rawPaths
     *
     * @return string[]
     */
    private function preparePaths(
        array|string $rawPaths,
        FileInterface $file,
    ): array {
        $paths = [];
        foreach ((array) $rawPaths as $path) {
            if (!is_string($path)) {
                throw new InvalidConfigurationException(sprintf(
                    'Path must be of type "string", "%s" given',
                    get_debug_type($path)
                ));
            }
            $paths[] = $this->preparePath($path, $file);
        }

        return $paths;
    }

    private function preparePath(
        string $path,
        FileInterface $file,
    ): string {
        if ('' === trim($path)) {
            throw new InvalidConfigurationException('Path must not be empty');
        }
        if ('/' !== $path[0]) {
            $path = $file->getParent() . '/' . $path;
        }

        return realpath($path) ?: $path;
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
