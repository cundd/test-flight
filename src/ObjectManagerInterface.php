<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:03
 */
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
    public function get($className, ...$constructorArguments);

    /**
     * Returns a new instance of the given class
     *
     * @param string $className
     * @param array  $constructorArguments
     * @return object
     */
    public function create($className, ...$constructorArguments);
}
