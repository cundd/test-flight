Event Dispatcher
================

A simple event dispatcher.


Register listeners
------------------

```php
$dispatcher = new \Cundd\TestFlight\Event\EventDispatcher();

// Functions
function increaseCounter(\Cundd\TestFlight\Event\EventInterface $event) {}
$dispatcher->register('my.event', 'increaseCounter');

// Static methods
class StaticMethodClass
{
    public static function method(\Cundd\TestFlight\Event\EventInterface $event) {}
}
$dispatcher->register('my.event', [StaticMethodClass::class, 'method']);

// Member methods
class MemberFunctionClass
{
    public function method(\Cundd\TestFlight\Event\EventInterface $event) {}
}
$dispatcher->register('my.event', [(new MemberFunctionClass()), 'method']);

// Callable classes
class CallableClass
{
    public function __invoke(\Cundd\TestFlight\Event\EventInterface $event) {}
}
$dispatcher->register('my.event', (new CallableClass()));

// Closures
$listenerClosure = function (\Cundd\TestFlight\Event\EventInterface $event) {};
$dispatcher->register('my.event', $listenerClosure);
```

Dispatch an event
-----------------

```php
class MyEvent implements \Cundd\TestFlight\Event\EventInterface {}

$dispatcher = new \Cundd\TestFlight\Event\EventDispatcher();
$event = new MyEvent();
$dispatcher->dispatch(
    'my.event',
    $event
);
```


Example
-------

```php

namespace Cundd\TestFlight\Event\Test;

class MyEvent implements \Cundd\TestFlight\Event\EventInterface {}

class Counter
{
    public static $closure = false;
    public static $function = false;
    public static $invokeClass = false;
    public static $invokeStatic = false;
    public static $invokeMember = false;
}

function increaseCounter(\Cundd\TestFlight\Event\EventInterface $event)
{
    Counter::$function = true;
}

class StaticMethodClass
{
    public static function method(\Cundd\TestFlight\Event\EventInterface $event)
    {
        Counter::$invokeStatic = true;
    }
}

class MemberFunctionClass
{
    public function method(\Cundd\TestFlight\Event\EventInterface $event)
    {
        Counter::$invokeMember = true;
    }
}

class CallableClass
{
    public function __invoke(\Cundd\TestFlight\Event\EventInterface $event)
    {
        Counter::$invokeClass = true;
    }
}

$dispatcher = new \Cundd\TestFlight\Event\EventDispatcher();
$listenerClosure = function () {
    Counter::$closure = true;
};
$dispatcher
    ->register('my.event', $listenerClosure)
    ->register('my.event', '\Cundd\TestFlight\Event\Test\increaseCounter')
    ->register('my.event', [(new MemberFunctionClass()), 'method'])
    ->register('my.event', (new CallableClass()))
    ->register('my.event', [StaticMethodClass::class, 'method']);


$event = new MyEvent();
$dispatcher->dispatch(
    'my.event',
    $event
);

test_flight_assert(Counter::$closure, 'Closure not executed');
test_flight_assert(Counter::$function, 'Function not executed');
test_flight_assert(Counter::$invokeClass, 'Callable class not executed');
test_flight_assert(Counter::$invokeStatic, 'Static method not executed');
test_flight_assert(Counter::$invokeMember, 'Member method not executed');
```