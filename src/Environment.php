<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:20
 */
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
     * @var array
     */
    private $get;

    /**
     * @var array
     */
    private $post;

    /**
     * @var array
     */
    private $cookie;

    /**
     * @var array
     */
    private $server;

    /**
     * @var array
     */
    private $session;

    /**
     * @var array
     */
    private $files;

    /**
     * @var array
     */
    private $request;

    /**
     * Environment constructor.
     *
     * @param string $timeZone
     * @param array  $locale
     * @param int    $errorReporting
     * @param array  $globals
     * @param array  $env
     * @param array  $get
     * @param array  $post
     * @param array  $cookie
     * @param array  $server
     * @param array  $session
     * @param array  $files
     * @param array  $request
     */
    public function __construct(
        $timeZone = '',
        array $locale = [],
        $errorReporting = 0,
        array $globals = [],
        array $env = [],
        array $get = [],
        array $post = [],
        array $cookie = [],
        array $server = [],
        array $session = [],
        array $files = [],
        array $request = []
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
     * @param string $timeZone
     * @param array  $locale
     * @param int    $errorReporting
     * @param array  $globals
     * @param array  $env
     * @param array  $get
     * @param array  $post
     * @param array  $cookie
     * @param array  $server
     * @param array  $session
     * @param array  $files
     * @param array  $request
     */
    public function store(
        $timeZone,
        array $locale,
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
    ) {
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
     *
     * @example
     */
    public function reset()
    {
        ini_set('display_errors', '0');
        error_reporting(E_ALL);

        ini_set('zend.assertions', '1');
        ini_set('assert.exception', '1');

        $GLOBALS = $this->prepareGlobalForRetrieval($this->globals);
        $_ENV = $this->prepareGlobalForRetrieval($this->env);
        $_GET = $this->prepareGlobalForRetrieval($this->get);
        $_POST = $this->prepareGlobalForRetrieval($this->post);
        $_COOKIE = $this->prepareGlobalForRetrieval($this->cookie);
        $_SERVER = $this->prepareGlobalForRetrieval($this->server);
        $_SESSION = $this->prepareGlobalForRetrieval($this->session);
        $_FILES = $this->prepareGlobalForRetrieval($this->files);
        $_REQUEST = $this->prepareGlobalForRetrieval($this->request);
    }

    /**
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return int
     */
    public function getErrorReporting()
    {
        return $this->errorReporting;
    }

    /**
     * @param $value
     * @return null|string
     */
    private function prepareGlobalForStorage($value)
    {
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected argument "value" to be of type array or NULL, %s given',
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }

        return serialize($value);
    }

    /**
     * @param $value
     * @return null|string
     */
    private function prepareGlobalForRetrieval($value)
    {
        if ($value === null) {
            return null;
        }

        return unserialize($value);
    }


    /**
     * @test
     */
    protected static function resetTest()
    {
        $_GET['my_get_var'] = 'get';
        $_POST['my_post_var'] = 'post';
        $_ENV['MY_ENV_VAR'] = 'nice';
        $locale = ["UTF-8", "C", "C", "C", "C", "C/UTF-8/C/C/C/C", "C"];
        $env = new Environment(
            'Europe/Vienna', $locale, E_ALL, $GLOBALS, $_ENV, $_GET, $_POST
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
