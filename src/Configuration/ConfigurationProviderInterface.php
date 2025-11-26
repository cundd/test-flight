<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Configuration;

/**
 * Interface for Configuration Providers
 */
interface ConfigurationProviderInterface
{
    public const LOCAL_CONFIGURATION_FILE_NAME = '.test-flight.json';

    /**
     * Returns the configuration for the given key
     *
     * <code>
     *  $cp = new \Cundd\TestFlight\Configuration\ConfigurationProvider(
     *      ['configuration' => __DIR__ . '/../../tests/resources/test-configuration.json']
     *  );
     *  test_flight_assert_same('/tests/resources/test-bootstrap.php', substr($cp->get('bootstrap'), -35));
     * </code>
     */
    public function get(string $key): mixed;

    /**
     * Sets the underlying configuration
     */
    public function setConfiguration(array $configuration): ConfigurationProviderInterface;
}
