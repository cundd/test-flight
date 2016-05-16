<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:03
 */
namespace Cundd\TestFlight;

use Cundd\TestFlight\Exception\ClassDoesNotExistException;
use Cundd\TestFlight\Exception\NoImplementationForInterfaceException;
use ReflectionClass;


/**
 * Class that manages the creation of instances of test classes
 */
class ObjectManager implements ObjectManagerInterface
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
        $this->container[ObjectManagerInterface::class] = $this;
    }

    /**
     * Retrieve the class from the container or creates a new instance
     *
     * @param string $className
     * @param array  $constructorArguments
     * @return object
     */
    public function get($className, ...$constructorArguments)
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
     * @param string $className
     * @param array  $constructorArguments
     * @return object
     */
    public function create($className, ...$constructorArguments)
    {
        return $this->createWithArguments($className, $constructorArguments);
    }

    /**
     * Returns a new instance of the given class
     *
     * @param string $className
     * @param array  $constructorArguments
     * @param string $implementationClassName
     * @return object
     */
    private function createWithArguments(
        $className,
        array $constructorArguments,
        &$implementationClassName = null
    ) {
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
     *
     * @param string $interfaceName
     * @return string
     */
    private function getClassForInterface($interfaceName)
    {
        $implementationName = '';
        if (strtolower(substr($interfaceName, -9)) === 'interface') {
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
    protected function getObjectSingletonTest()
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
    protected function getObjectInterfaceTest()
    {
        test_flight_assert($this->get(ObjectManagerInterface::class) === $this);
    }

    /**
     * @test
     */
    protected static function getSharedInstanceTest()
    {
        test_flight_assert(self::sharedInstance() instanceof ObjectManager);
        test_flight_assert(self::sharedInstance()->get(__CLASS__) === self::sharedInstance());
    }
}
