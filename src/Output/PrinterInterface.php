<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 21/04/16
 * Time: 21:27
 */

namespace Cundd\TestFlight\Output;


interface PrinterInterface
{
    const NORMAL = "\033[0m";

    const BLACK = "\033[0;30m";
    const DARK_GRAY = "\033[1;30m";
    const BLUE = "\033[0;34m";
    const LIGHT_BLUE = "\033[1;34m";
    const GREEN = "\033[0;32m";
    const LIGHT_GREEN = "\033[1;32m";
    const CYAN = "\033[0;36m";
    const LIGHT_CYAN = "\033[1;36m";
    const RED = "\033[0;31m";
    const LIGHT_RED = "\033[1;31m";
    const PURPLE = "\033[0;35m";
    const LIGHT_PURPLE = "\033[1;35m";
    const BROWN = "\033[0;33m";
    const YELLOW = "\033[1;33m";
    const LIGHT_GRAY = "\033[0;37m";
    const WHITE = "\033[1;37m";

    const BLACK_BACKGROUND = "\033[40m";
    const RED_BACKGROUND = "\033[41m";
    const GREEN_BACKGROUND = "\033[42m";
    const YELLOW_BACKGROUND = "\033[43m";
    const BLUE_BACKGROUND = "\033[44m";
    const MAGENTA_BACKGROUND = "\033[45m";
    const CYAN_BACKGROUND = "\033[46m";
    const LIGHT_GRAY_BACKGROUND = "\033[47m";

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
