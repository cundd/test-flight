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
     * @var resource
     */
    private $errorStream;

    /**
     * @var boolean
     */
    private $enableColoredOutput = true;

    /**
     * @var bool
     */
    private $verbose = false;

    /**
     * Printer constructor.
     *
     * @param resource $outputStream
     * @param resource $errorStream
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
    public function printf(string $format, ...$arguments)
    {
        return $this->printWithArray($format, $arguments);
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function println(string $format, ...$arguments)
    {
        return $this->printWithArray($format.PHP_EOL, $arguments);
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function printError(string $format, ...$arguments)
    {
        if ($this->getEnableColoredOutput()) {
            $format = self::RED.$format.self::NORMAL.PHP_EOL;
        } else {
            $format .= PHP_EOL;
        }
        fwrite($this->errorStream, vsprintf($format, $arguments));

        return $this;
    }

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function debug(string $format, ...$arguments)
    {
        if ($this->getVerbose()) {
            $this->printWithArray($format, $arguments);
        }

        return $this;
    }


    /**
     * Returns if colors should be enabled
     *
     * @return boolean
     */
    public function getEnableColoredOutput(): bool
    {
        if (!$this->getCliHasColorSupport()) {
            return false;
        }

        return $this->enableColoredOutput;
    }

    /**
     * Set if colored output should be enabled
     *
     * @param boolean $enableColoredOutput
     * @return Printer
     */
    public function setEnableColoredOutput(bool $enableColoredOutput)
    {
        $this->enableColoredOutput = (bool)$enableColoredOutput;

        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setVerbose(bool $flag = false)
    {
        $this->verbose = $flag;

        return $this;
    }

    /**
     * @return bool
     */
    public function getVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * @return bool
     */
    protected function getCliHasColorSupport()
    {
        if (isset($_SERVER['TERM'])) {
            return in_array($_SERVER['TERM'], ['xterm-256color']);
        }

        return false;
    }

    /**
     * @param string $format
     * @param array  $arguments
     * @return $this
     */
    private function printWithArray($format, array $arguments)
    {
        fwrite($this->outputStream, vsprintf($format, $arguments));

        return $this;
    }
}
