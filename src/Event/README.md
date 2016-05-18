Event Dispatcher
================

A simple event dispatcher.


Register listeners
------------------

```php
$dispatcher = new \Cundd\TestFlight\Event\EventDispatcher();

// Functions
function increaseCounter() {}
$dispatcher->register('my.event', 'increaseCounter');

// Static methods
class StaticMethodClass
{
    public static function method() {}
}
$dispatcher->register('my.event', [StaticMethodClass::class, 'method']);

// Member methods
class MemberFunctionClass
{
    public function method() {}
}
$dispatcher->register('my.event', [(new MemberFunctionClass()), 'method']);

// Callable classes
class CallableClass
{
    public function __invoke() {}
}
$dispatcher->register('my.event', (new CallableClass()));

// Closures
$listenerClosure = function (\Cundd\TestFlight\Definition\DefinitionInterface $definition) {
};
$dispatcher->register('my.event', $listenerClosure);
```

Dispatch an event
-----------------

```php
$dispatcher = new \Cundd\TestFlight\Event\EventDispatcher();

$dummyFile = new \Cundd\TestFlight\FileAnalysis\File(__FILE__);
$dispatcher->dispatch(
    'my.event',
    new Cundd\TestFlight\Definition\MethodDefinition('', '', $dummyFile)
);
```


Example
-------

```php
namespace Cundd\TestFlight\Event\Test;

class Counter
{
    public static $closure = false;
    public static $function = false;
    public static $invokeClass = false;
    public static $invokeStatic = false;
    public static $invokeMember = false;
}

function increaseCounter()
{
    Counter::$function = true;
}

class StaticMethodClass
{
    public static function method()
    {
        Counter::$invokeStatic = true;
    }
}

class MemberFunctionClass
{
    public function method()
    {
        Counter::$invokeMember = true;
    }
}

class CallableClass
{
    public function __invoke()
    {
        Counter::$invokeClass = true;
    }
}

$dispatcher = new \Cundd\TestFlight\Event\EventDispatcher();
$listenerClosure = function (\Cundd\TestFlight\Definition\DefinitionInterface $definition) {
    Counter::$closure = true;
};
$dispatcher
    ->register('my.event', $listenerClosure)
    ->register('my.event', '\Cundd\TestFlight\Event\Test\increaseCounter')
    ->register('my.event', [(new MemberFunctionClass()), 'method'])
    ->register('my.event', (new CallableClass()))
    ->register('my.event', [StaticMethodClass::class, 'method']);

$dummyFile = new \Cundd\TestFlight\FileAnalysis\File(__FILE__);
$dispatcher->dispatch(
    'my.event',
    new \Cundd\TestFlight\Definition\MethodDefinition('f', 'b', $dummyFile)
);

test_flight_assert(Counter::$closure, 'Closure not executed');
test_flight_assert(Counter::$function, 'Function not executed');
test_flight_assert(Counter::$invokeClass, 'Callable class not executed');
test_flight_assert(Counter::$invokeStatic, 'Static method not executed');
test_flight_assert(Counter::$invokeMember, 'Member method not executed');
```