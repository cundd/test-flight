Test Context
============

Register a listener to add variables in the scope of the ran tests.

Example `bootstrap-file.php`:

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

The actual test:

```php
test_flight_assert_true(isset($aVariableSetInBootstrap));
test_flight_assert_same(
	'variable-was-set-in-bootstrap',
	$aVariableSetInBootstrap
);
```

To perform this test run 

```bash
bin/test-flight --bootstrap tests/resources/test-bootstrap.php src/Context/README.md
```