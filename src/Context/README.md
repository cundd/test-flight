Test Context
============

Sometimes it makes sense that only a part of code is defined in the Documentation or DocComment tests. As an example not all variables used are defined inside the code block.

Assume that the `$eventDispatcher` has already been created elsewhere:

```php
$eventDispatcher->register(
    \Cundd\TestFlight\TestRunner\TestRunnerInterface::EVENT_TEST_WILL_RUN,
    function (\Cundd\TestFlight\Event\Event $event) {
        $event->getContext()->setVariable('aVariableSetInBootstrap', 'variable-was-set-in-bootstrap');
    }
);
```

This can be achieved in a few steps:

1. Create a bootstrap PHP file
2. Register a listener for the event `\Cundd\TestFlight\TestRunner\TestRunnerInterface::EVENT_TEST_WILL_RUN`
3. Add variables to the event's context object
4. Run the Test-Flight command with the bootstrap file 
	
	`bin/test-flight --bootstrap tests/resources/test-bootstrap.php src/Context/README.md`

 

Example
-------

### File `bootstrap-file.php`:

```
// Define the variable '$aVariableSetInBootstrap' for the following test
/** @var \Cundd\TestFlight\Event\EventDispatcherInterface $eventDispatcher */
$eventDispatcher->register(
    \Cundd\TestFlight\TestRunner\TestRunnerInterface::EVENT_TEST_WILL_RUN,
    function (\Cundd\TestFlight\Event\Event $event) {
        $event->getContext()->setVariable('aVariableSetInBootstrap', 'variable-was-set-in-bootstrap');
    }
);
```

### The actual test:

```php
test_flight_assert_true(isset($aVariableSetInBootstrap));
test_flight_assert_same(
	'variable-was-set-in-bootstrap',
	$aVariableSetInBootstrap
);
```

### Run this test

```bash
bin/test-flight --bootstrap tests/resources/test-bootstrap.php src/Context/README.md
```