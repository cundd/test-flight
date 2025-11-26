<?php

declare(strict_types=1);

namespace Cundd\TestFlight;

use Cundd\TestFlight\Exception\ClassDoesNotExistException;
use Cundd\TestFlight\Exception\NoImplementationForInterfaceException;
use ReflectionClass;

/**
 * Class that manages the creation of instances of test classes
 */
final class ObjectManager implements ObjectManagerInterface
{
    /**
     * @var array<class-string, object>
     */
    private array $container = [];

    /**
     * Retrieve a shared instance of the ObjectManager
     */
    public static function sharedInstance(): ObjectManagerInterface
    {
        static $sharedInstance;
        if (!$sharedInstance) {
            $sharedInstance = new static();
        }

        return $sharedInstance;
    }

    public function __construct()
    {
        $this->container[__CLASS__] = $this;
        $this->container[ObjectManagerInterface::class] = $this;
    }

    /**
     * Retrieve the class from the container or creates a new instance
     *
     * @param array $constructorArguments
     */
    public function get(string $className, ...$constructorArguments): object
    {
        if (!isset($this->container[$className])) {
            $instance = $this->createWithArguments(
                $className,
                $constructorArguments,
                $implementationClassName
            );

            $this->container[$className] = $instance;
            if ($implementationClassName) {
                $this->container[$implementationClassName] = $instance;
            }
        }

        return $this->container[$className];
    }

    /**
     * Returns a new instance of the given class
     *
     * @param array $constructorArguments
     */
    public function create(string $className, ...$constructorArguments): object
    {
        return $this->createWithArguments($className, $constructorArguments);
    }

    /**
     * Returns a new instance of the given class
     */
    private function createWithArguments(
        string $className,
        array $constructorArguments,
        ?string &$implementationClassName = null,
    ): object {
        if (interface_exists($className)) {
            $className = $implementationClassName = $this->getClassForInterface($className);
        }
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
     * Try to find the class name for the given interface
     */
    private function getClassForInterface(string $interfaceName): string
    {
        $implementationName = '';
        if ('interface' === strtolower(substr($interfaceName, -9))) {
            $implementationName = substr($interfaceName, 0, -9);
        }

        if (!class_exists($implementationName)) {
            throw NoImplementationForInterfaceException::exceptionWithInterfaceName($interfaceName);
        }

        return $implementationName;
    }

    /**
     * @test
     */
    protected function getObjectSingletonTest(): void
    {
        test_flight_assert($this->get(__CLASS__) === $this);
        test_flight_assert($this->get(__CLASS__) === $this->get(__CLASS__));
        test_flight_assert($this->get(__CLASS__) === $this->get(ObjectManager::class));
        test_flight_assert($this->get(__CLASS__) === $this->get('Cundd\\TestFlight\\ObjectManager'));

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
    protected function getObjectInterfaceTest(): void
    {
        test_flight_assert($this->get(ObjectManagerInterface::class) === $this);
    }

    /**
     * @test
     */
    protected static function getSharedInstanceTest(): void
    {
        test_flight_assert(self::sharedInstance() instanceof ObjectManager);
        test_flight_assert(self::sharedInstance()->get(__CLASS__) === self::sharedInstance());
    }
}
