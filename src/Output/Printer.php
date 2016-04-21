<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:33
 */

namespace Cundd\TestFlight\Output;


class Printer implements PrinterInterface
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
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function println(string $format, ...$arguments)
    {
        $format .= PHP_EOL;
        fwrite($this->outputStream, vsprintf($format, $arguments));

        return $this;
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function printError(string $format, ...$arguments)
    {
        if ($this->getCliHasColorSupport()) {
            $format = self::RED.$format.self::NORMAL.PHP_EOL;
        } else {
            $format .= PHP_EOL;
        }
        fwrite($this->outputStream, vsprintf($format, $arguments));

        return $this;
    }

    /**
     * @return bool
     */
    private function getCliHasColorSupport()
    {
        if (isset($_SERVER['TERM'])) {
            return in_array($_SERVER['TERM'], ['xterm-256color']);
        }

        return false;
    }
}
