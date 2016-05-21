<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21/05/16
 * Time: 10:47
 */

namespace Cundd\TestFlight\Configuration;


use Cundd\TestFlight\Configuration\Exception\InvalidConfigurationException;
use Cundd\TestFlight\Configuration\Exception\InvalidFileTypeException;
use Cundd\TestFlight\Configuration\Exception\InvalidJsonException;
use Cundd\TestFlight\FileAnalysis\File;
use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * Configuration Provider
 */
class ConfigurationProvider implements ConfigurationProviderInterface
{
    /**
     * @var array
     */
    private $configuration;

    /**
     * ConfigurationProvider constructor.
     *
     * @param array $coreConfiguration The core configuration (provided through CLI options)
     */
    public function __construct(array $coreConfiguration = [])
    {
        if (isset($coreConfiguration['configuration'])) {
            $coreConfiguration = array_merge(
                $this->load($coreConfiguration['configuration']),
                $coreConfiguration
            );
        }
        $this->configuration = $coreConfiguration;
    }


    /**
     * Returns the configuration for the given key
     *
     * <code>
     *  $cp = new \Cundd\TestFlight\Configuration\ConfigurationProvider(
     *      ['configuration' => __DIR__ . '/../../tests/resources/test-configuration.json']
     *  );
     *  test_flight_assert_same('/tests/resources/test-bootstrap.php', substr($cp->get('bootstrap'), -35));
     * </code>
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->configuration[$key] ?? null;
    }

    /**
     * @param string $path
     * @return array
     */
    private function load($path): array
    {
        $file = new File($path);

        if ($file->getExtension() !== 'json') {
            throw new InvalidFileTypeException(
                sprintf('File type %s not supported (currently only JSON files are supported)', $file->getExtension()),
                1463827284
            );
        }

        $data = json_decode($file->getContents(), true);
        if (json_last_error() > 0) {
            throw new InvalidJsonException(json_last_error_msg(), 1463827638);
        }
        if (!is_array($data)) {
            throw new InvalidConfigurationException('JSON configuration data must be an array', 1463827639);
        }

        if (isset($data['bootstrap'])) {
            $data['bootstrap'] = $this->preparePath($data['bootstrap'], $file);
        }

        return $data;
    }

    /**
     * @param string        $inputPath
     * @param FileInterface $file
     * @return string
     */
    private function preparePath(string $inputPath, FileInterface $file): string
    {
        if (!$inputPath) {
            return '';
        }
        if ($inputPath[0] !== '/') {
            $inputPath = $file->getParent().'/'.$inputPath;
        }

        return realpath($inputPath) ?: $inputPath;
    }

    /**
     * @test
     */
    protected function createTest()
    {
        test_flight_throws(
            function () {
                new self(
                    ['configuration' => __DIR__.'/../../tests/resources/test-configuration-invalid-json.json']
                );
            },
            InvalidJsonException::class
        );
        test_flight_throws(
            function () {
                new self(
                    ['configuration' => __DIR__.'/../../tests/resources/test-configuration-invalid-data.json']
                );
            },
            InvalidConfigurationException::class
        );
    }
}
