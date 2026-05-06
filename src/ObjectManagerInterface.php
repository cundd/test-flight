<?php

declare(strict_types=1);

namespace Cundd\TestFlight;

/**
 * Interface for classes that manages the creation of instances of test classes
 */
interface ObjectManagerInterface
{
    /**
     * Retrieve the class from the container or creates a new instance
     *
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return T
     */
    public function get(
        string $className,
        mixed ...$constructorArguments,
    ): object;

    /**
     * Return a new instance of the given class
     *
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return T
     */
    public function create(
        string $className,
        mixed ...$constructorArguments,
    ): object;
}
