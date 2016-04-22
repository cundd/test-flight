<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 21:33
 */

namespace Cundd\TestFlight\Output;

/**
 * Printer implementation
 */
class Printer implements PrinterInterface
{
    /**
     * @var resource
     */
    private $outputStream;

    /**
     * @var
     */
    private $errorStream;

    /**
     * Printer constructor.
     *
     * @param resource $outputStream
     * @param          $errorStream
     */
    public function __construct($outputStream, $errorStream)
    {
        $this->outputStream = $outputStream;
        $this->errorStream = $errorStream;
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
        fwrite($this->errorStream, vsprintf($format, $arguments));

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
