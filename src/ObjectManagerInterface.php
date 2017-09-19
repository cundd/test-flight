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
    public static function sharedInstance();

    /**
     * Retrieve the class from the container or creates a new instance
     *
     * @param string $className
     * @param array  $constructorArguments
     * @return object
     */
    public function get(string $className, ...$constructorArguments);

    /**
     * Returns a new instance of the given class
     *
     * @param string $className
     * @param array  $constructorArguments
     * @return object
     */
    public function create(string $className, ...$constructorArguments);
}
