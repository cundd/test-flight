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
    public function printf(string $format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function println(string $format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function printError(string $format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function info(string $format, ...$arguments);

    /**
     * @param string $format
     * @param array  ...$arguments
     * @return $this
     */
    public function debug(string $format, ...$arguments);

    /**
     * @param bool $flag
     * @return $this
     */
    public function setVerbose(bool $flag = false);

    /**
     * @return bool
     */
    public function getVerbose(): bool;
}
