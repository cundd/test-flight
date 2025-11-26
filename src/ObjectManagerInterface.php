<?php

declare(strict_types=1);

namespace Cundd\TestFlight;

/**
 * Interface for classes that manages the creation of instances of test classes
 */
interface ObjectManagerInterface
{
    /**
     * Retrieve a shared instance of the ObjectManager
     *
     * @return ObjectManager
     */
    public static function sharedInstance(): ObjectManagerInterface;

    /**
     * Retrieve the class from the container or creates a new instance
     *
     * @param array $constructorArguments
     */
    public function get(string $className, ...$constructorArguments): object;

    /**
     * Returns a new instance of the given class
     *
     * @param array $constructorArguments
     */
    public function create(string $className, ...$constructorArguments): object;
}
