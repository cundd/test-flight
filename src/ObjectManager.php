<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:03
 */
declare(strict_types = 1);

namespace Cundd\TestFlight;

use ReflectionClass;


/**
 * Class that manages the creation of instances of test classes
 *
 * @package Cundd\TestFlight
 */
class ObjectManager
{
    /**
     * @var array
     */
    private $container = [];

    /**
     * Retrieve the class from the container or creates a new instance
     *
     * @param string $className
     * @param array  $constructorArguments
     * @return object
     */
    public function get(string $className, ...$constructorArguments)
    {
        if (!isset($this->container[$className])) {
            $this->container[$className] = $this->createInstanceOfClassWithArguments(
                $className,
                $constructorArguments
            );
        }

        return $this->container[$className];
    }

    /**
     * Returns a new instance of the given class
     *
     * @param string $className
     * @param array  $constructorArguments
     * @return object
     */
    public function createInstanceOfClass(string $className, ...$constructorArguments)
    {
        return $this->createInstanceOfClassWithArguments($className, $constructorArguments);
    }

    /**
     * Returns a new instance of the given class
     *
     * @param string $className
     * @param array  $constructorArguments
     * @return object
     */
    private function createInstanceOfClassWithArguments(string $className, array $constructorArguments)
    {
        if ($constructorArguments) {
            $reflector = new ReflectionClass($className);

            return $reflector->newInstanceArgs($constructorArguments);
        }

        return new $className();
    }
}
