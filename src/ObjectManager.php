<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:03
 */
declare(strict_types = 1);

namespace Cundd\TestFlight;

use Cundd\TestFlight\Exception\ClassDoesNotExistException;
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
     * Retrieve a shared instance of the ObjectManager
     *
     * @return ObjectManager
     */
    public static function sharedInstance()
    {
        static $sharedInstance = null;
        if (!$sharedInstance) {
            $sharedInstance = new static();
        }

        return $sharedInstance;
    }

    /**
     * ObjectManager constructor
     */
    public function __construct()
    {
        $this->container[__CLASS__] = $this;
    }

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
        if (!class_exists($className)) {
            throw ClassDoesNotExistException::exceptionWithClassName($className);
        }
        if ($constructorArguments) {
            $reflector = new ReflectionClass($className);

            return $reflector->newInstanceArgs($constructorArguments);
        }

        return new $className();
    }

    /**
     * @test
     */
    protected function getObjectSingletonTest()
    {
        assert($this->get(__CLASS__) === $this);
        assert($this->get(__CLASS__) === $this->get(__CLASS__));
        assert($this->get(__CLASS__) === $this->get(ObjectManager::class));
        assert($this->get(__CLASS__) === $this->get('Cundd\\TestFlight\\ObjectManager'));

        Assert::throws(
            function () {
                $this->get('Not_Existing_Class');
            },
            ClassDoesNotExistException::class
        );
    }

    /**
     * @test
     */
    protected static function getSharedInstanceTest()
    {
        assert(self::sharedInstance() instanceof ObjectManager);
        assert(self::sharedInstance()->get(__CLASS__) === self::sharedInstance());


    }
}
