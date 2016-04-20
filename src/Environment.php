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
     * Environment constructor.
     *
     * @param array  $env
     * @param string $timeZone
     * @param array  $locale
     * @param int    $errorReporting
     */
    public function __construct(array $env = [], $timeZone = '', array $locale = [], $errorReporting = 0)
    {
        $this->store($env, $timeZone, $locale, $errorReporting);
    }

    /**
     * Environment constructor.
     *
     * @param array  $env
     * @param string $timeZone
     * @param array  $locale
     * @param int    $errorReporting
     */
    public function store(array $env, string $timeZone, array $locale, int $errorReporting)
    {
        $this->env = $env;
        $this->timeZone = $timeZone;
        $this->locale = $locale;
        $this->errorReporting = intval($errorReporting);
    }

    /**
     * Set the environment variables
     */
    public function reset()
    {
        ini_set('display_errors', '0');
        error_reporting(E_ALL);

        ini_set('zend.assertions', '1');
        ini_set('assert.exception', '1');
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
     * @return array
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
