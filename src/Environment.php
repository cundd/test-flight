<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:20
 */
declare(strict_types = 1);
namespace Cundd\TestFlight;


class Environment
{
    /**
     * @var array
     */
    private $env;

    /**
     * @var string
     */
    private $timeZone;

    /**
     * @var array
     */
    private $locale;

    /**
     * @var int
     */
    private $errorReporting;

    /**
     * @var array
     */
    private $globals;

    /**
     * Environment constructor.
     *
     * @param array  $env
     * @param string $timeZone
     * @param array  $locale
     * @param int    $errorReporting
     * @param array  $globals
     */
    public function __construct(
        array $env = [],
        $timeZone = '',
        array $locale = [],
        $errorReporting = 0,
        array $globals = []
    ) {
        $this->store($env, $timeZone, $locale, $errorReporting, $globals);
    }

    /**
     * Environment constructor.
     *
     * @param array  $env
     * @param string $timeZone
     * @param array  $locale
     * @param int    $errorReporting
     * @param array  $globals
     */
    public function store(array $env, string $timeZone, array $locale, int $errorReporting, array $globals)
    {
        $this->env = $env;
        $this->timeZone = $timeZone;
        $this->locale = $locale;
        $this->errorReporting = intval($errorReporting);
        $this->globals = serialize($globals);
    }

    /**
     * @test
     */
    protected static function restoreTest()
    {
        $_GET['my_get_var'] = 'get';
        $_POST['my_post_var'] = 'post';
        $_ENV['MY_ENV_VAR'] = 'nice';
        $locale = ["UTF-8", "C", "C", "C", "C", "C/UTF-8/C/C/C/C", "C"];
        $env = new \Cundd\TestFlight\Environment($_ENV, 'Europe/Vienna', $locale, E_ALL, $GLOBALS);

        $_GET['my_get_var'] = 'something else';
        $_POST['my_post_var'] = 'something else';
        $_ENV['MY_ENV_VAR'] = 'something else';

        var_dump($GLOBALS);

        $env->reset();

        test_flight_assert_same('get', $_GET['my_get_var']);
        test_flight_assert_same('post', $_POST['my_post_var']);
        test_flight_assert_same('nice', $_ENV['MY_ENV_VAR']);
    }


    /**
     * Reset the environment variables
     *
     * @example
     */
    public function reset()
    {
        ini_set('display_errors', '0');
        error_reporting(E_ALL);

        ini_set('zend.assertions', '1');
        ini_set('assert.exception', '1');

        $GLOBALS = unserialize($this->globals);
        if (isset($GLOBALS['_GET'])) {
            $_GET = $GLOBALS['_GET'];
        }
        if (isset($GLOBALS['_POST'])) {
            $_POST = $GLOBALS['_POST'];
        }
        if (isset($GLOBALS['_COOKIE'])) {
            $_COOKIE = $GLOBALS['_COOKIE'];
        }
        if (isset($GLOBALS['_SERVER'])) {
            $_SERVER = $GLOBALS['_SERVER'];
        }
        if (isset($GLOBALS['_SESSION '])) {
            $_SESSION = $GLOBALS['_SESSION '];
        }
        if (isset($GLOBALS['_FILES'])) {
            $_FILES = $GLOBALS['_FILES'];
        }
        if (isset($GLOBALS['_REQUEST'])) {
            $_REQUEST = $GLOBALS['_REQUEST'];
        }
    }

    /**
     * @return array
     */
    public function getEnv(): array
    {
        return $this->env;
    }

    /**
     * @return string
     */
    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @return int
     */
    public function getErrorReporting(): int
    {
        return $this->errorReporting;
    }
}
