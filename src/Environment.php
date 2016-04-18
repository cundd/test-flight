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
     * Environment constructor.
     *
     * @param array         $env
     * @param string $timeZone
     * @param array        $locale
     * @param int           $errorReporting
     */
    public function __construct(array $env, $timeZone, array $locale, $errorReporting)
    {
        $this->env            = $env;
        $this->timeZone       = $timeZone;
        $this->locale         = $locale;
        $this->errorReporting = intval($errorReporting);
    }

    /**
     *
     */
    public function reset()
    {
//        ini_set('display_errors', false);
        error_reporting(E_ALL);
        ini_set('zend.assertions', 1);
        ini_set('assert.exception', 1);
    }

    /**
     * @return array
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @return string
     */
    public function getTimeZone()
    {
        return $this->timeZone;
    }

    /**
     * @return array
     */
    public function getLocale()
    {
        return $this->locale;
    }
}
