<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21/04/16
 * Time: 21:27
 */

namespace Cundd\TestFlight\Output;

interface PrinterInterface extends ColorInterface
{
    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function printf($format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function println($format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function printError($format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function info($format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function warn($format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function debug($format, ...$arguments);

    /**
     * Returns if colors should be enabled
     *
     * @return boolean
     */
    public function getEnableColoredOutput();

    /**
     * Set if colored output should be enabled
     *
     * @param boolean $enableColoredOutput
     * @return PrinterInterface
     */
    public function setEnableColoredOutput($enableColoredOutput);

    /**
     * Wrap the text in colors
     *
     * @param string $startColor
     * @param string $text
     * @param string $endColor
     * @return string
     */
    public function colorize($startColor, $text, $endColor = self::NORMAL);

    /**
     * @param bool $flag
     * @return $this
     */
    public function setVerbose($flag = false);

    /**
     * @return bool
     */
    public function getVerbose();
}
