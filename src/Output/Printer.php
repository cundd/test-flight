<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:33
 */

namespace Cundd\TestFlight\Output;


class Printer
{
    /**
     * @var resource
     */
    private $outputStream;

    /**
     * Printer constructor.
     *
     * @param resource $outputStream
     */
    public function __construct($outputStream)
    {
        $this->outputStream = $outputStream;
    }

    /**
     * @param $format
     * @param $argN
     */
    public function println($format, $argN)
    {
        $arguments = func_get_args();
        $format    = array_shift($arguments).PHP_EOL;
        fwrite($this->outputStream, vsprintf($format, $arguments));
    }
}
