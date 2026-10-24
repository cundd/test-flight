<?php

declare(strict_types=1);

namespace Cundd\TestFlight;

use InvalidArgumentException;

final class Environment
{
    private ?string $env;
    private string $timeZone;
    /**
     * @var array<int,string|false>
     */
    private array $locale;
    private int $errorReporting;
    private ?string $globals;
    private ?string $get;
    private ?string $post;
    private ?string $cookie;
    private ?string $server;
    private ?string $session;
    private ?string $files;
    private ?string $request;

    /**
     * @param array<int,string|false> $locale
     * @param array<int,mixed>        $globals
     * @param array<mixed>            $env
     * @param array<mixed>            $get
     * @param array<mixed>            $post
     * @param array<int,mixed>        $cookie
     * @param array<int,mixed>        $server
     * @param array<int,mixed>        $session
     * @param array<int,mixed>        $files
     * @param array<int,mixed>        $request
     */
    public function __construct(
        string $timeZone = '',
        array $locale = [],
        int $errorReporting = 0,
        array $globals = [],
        array $env = [],
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $server = [],
        array $session = [],
        array $files = [],
        array $request = [],
    ) {
        $this->store(
            $timeZone,
            $locale,
            $errorReporting,
            $globals,
            $env,
            $get,
            $post,
            $cookie,
            $server,
            $session,
            $files,
            $request
        );
    }

    /**
     * Store the given environment data
     *
     * @param array<int,string|false> $locale
     * @param array<int|string,mixed> $globals
     * @param array<int|string,mixed> $env
     * @param array<int|string,mixed> $get
     * @param array<int|string,mixed> $post
     * @param array<int|string,mixed> $cookie
     * @param array<int|string,mixed> $server
     * @param array<int|string,mixed> $session
     * @param array<int|string,mixed> $files
     * @param array<int|string,mixed> $request
     */
    public function store(
        string $timeZone,
        array $locale,
        int $errorReporting,
        array $globals,
        array $env,
        array $get,
        array $post,
        array $cookie,
        array $server,
        array $session,
        array $files,
        array $request,
    ): void {
        $this->timeZone = $timeZone;
        $this->locale = $locale;
        $this->errorReporting = intval($errorReporting);

        $this->globals = $this->prepareGlobalForStorage($globals);
        $this->env = $this->prepareGlobalForStorage($env);
        $this->get = $this->prepareGlobalForStorage($get);
        $this->post = $this->prepareGlobalForStorage($post);
        $this->cookie = $this->prepareGlobalForStorage($cookie);
        $this->server = $this->prepareGlobalForStorage($server);
        $this->session = $this->prepareGlobalForStorage($session);
        $this->files = $this->prepareGlobalForStorage($files);
        $this->request = $this->prepareGlobalForStorage($request);
    }

    /**
     * Reset the environment variables
     */
    public function reset(): void
    {
        ini_set('display_errors', '0');
        error_reporting(E_ALL);

        ini_set('zend.assertions', '1');
        ini_set('assert.exception', '1');

        $this->patchGlobals($this->prepareGlobalForRetrieval($this->globals));
        $_ENV = $this->prepareGlobalForRetrieval($this->env);
        $_GET = $this->prepareGlobalForRetrieval($this->get);
        $_POST = $this->prepareGlobalForRetrieval($this->post);
        $_COOKIE = $this->prepareGlobalForRetrieval($this->cookie);
        $_SERVER = $this->prepareGlobalForRetrieval($this->server);
        $_SESSION = $this->prepareGlobalForRetrieval($this->session);
        $_FILES = $this->prepareGlobalForRetrieval($this->files);
        $_REQUEST = $this->prepareGlobalForRetrieval($this->request);
    }

    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    /**
     * @return array<int,string|false>
     */
    public function getLocale(): array
    {
        return $this->locale;
    }

    public function getErrorReporting(): int
    {
        return $this->errorReporting;
    }

    private function prepareGlobalForStorage(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }
        if (!is_array($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected argument "value" to be of type array or NULL, %s given',
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        return serialize($value);
    }

    private function prepareGlobalForRetrieval(mixed $value): mixed
    {
        if (null === $value) {
            return null;
        }

        return unserialize($value);
    }

    /**
     * @param array<int|string,mixed> $restoredValues
     */
    private function patchGlobals(array $restoredValues): void
    {
        foreach ($restoredValues as $key => $value) {
            $GLOBALS[$key] = $value;
        }
        foreach (array_keys($GLOBALS) as $key) {
            if (!in_array($key, array_keys($restoredValues))) {
                unset($GLOBALS[$key]);
            }
        }
    }

    /**
     * @test
     *
     * @phpstan-ignore method.unused
     */
    private static function resetTest(): void
    {
        $_GET['my_get_var'] = 'get';
        $_POST['my_post_var'] = 'post';
        $_ENV['MY_ENV_VAR'] = 'nice';
        $locale = ['UTF-8', 'C', 'C', 'C', 'C', 'C/UTF-8/C/C/C/C', 'C'];
        $env = new Environment(
            'Europe/Vienna',
            $locale,
            E_ALL,
            $GLOBALS,
            $_ENV,
            $_GET,
            $_POST
        );

        $_GET['my_get_var'] = 'something else';
        $_POST['my_post_var'] = 'something else';
        $_ENV['MY_ENV_VAR'] = 'something else';

        $env->reset();

        test_flight_assert_same('get', $_GET['my_get_var']);
        test_flight_assert_same('post', $_POST['my_post_var']);
        test_flight_assert_same('nice', $_ENV['MY_ENV_VAR']);
    }
}
