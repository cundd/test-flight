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

    public function __construct()
    {
        $this->container[__CLASS__] = $this;
        $this->container[ObjectManagerInterface::class] = $this;
    }

    public function get(
        string $className,
        mixed ...$constructorArguments,
    ): object {
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

            return $instance;
        }

        // @phpstan-ignore return.type
        return $this->container[$className];
    }

    public function create(
        string $className,
        mixed ...$constructorArguments,
    ): object {
        return $this->createWithArguments($className, $constructorArguments);
    }

    /**
     * Return a new instance of the given class
     *
     * @template T of object
     *
     * @param class-string<T> $classOrInterfaceName
     * @param mixed[]         $constructorArguments
     * @param class-string<T> $implementationClassName
     *
     * @return T
     */
    private function createWithArguments(
        string $classOrInterfaceName,
        array $constructorArguments,
        ?string &$implementationClassName = null,
    ): object {
        if (interface_exists($classOrInterfaceName)) {
            $className = $implementationClassName = $this->getClassForInterface($classOrInterfaceName);
        } else {
            $className = $classOrInterfaceName;
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
     *
     * @template T
     *
     * @param interface-string<T> $interfaceName
     *
     * @return class-string<T>
     */
    private function getClassForInterface(string $interfaceName): string
    {
        $implementationName = '';
        if ('interface' === strtolower(substr($interfaceName, -9))) {
            $implementationName = substr($interfaceName, 0, -9);
        }

        if (!class_exists($implementationName)) {
            throw NoImplementationForInterfaceException::exceptionWithInterfaceName(
                $interfaceName
            );
        }

        if (!is_a($implementationName, $interfaceName, true)) {
            throw NoImplementationForInterfaceException::exceptionWithInterfaceName(
                $interfaceName
            );
        }

        return $implementationName;
    }

    /**
     * @test
     *
     * @phpstan-ignore method.unused
     */
    private function getObjectSingletonTest(): void
    {
        test_flight_assert($this->get(__CLASS__) === $this);
        test_flight_assert($this->get(__CLASS__) === $this->get(__CLASS__));
        test_flight_assert($this->get(__CLASS__) === $this->get(ObjectManager::class));
        test_flight_assert($this->get(__CLASS__) === $this->get('Cundd\\TestFlight\\ObjectManager'));

        Assert::throws(
            function () {
                $this->get('Not_Existing_Class'); // @phpstan-ignore argument.type
            },
            ClassDoesNotExistException::class
        );
    }

    /**
     * @test
     *
     * @phpstan-ignore method.unused
     */
    private function getObjectInterfaceTest(): void
    {
        test_flight_assert($this->get(ObjectManagerInterface::class) === $this);
    }
}
