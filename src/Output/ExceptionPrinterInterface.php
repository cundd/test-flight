<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 24/04/16
 * Time: 18:00
 */
namespace Cundd\TestFlight\Output;

use Cundd\TestFlight\Definition;


/**
 * Special printer interface for exception output
 */
interface ExceptionPrinterInterface
{
    /**
     * Prints the exception for the given test definition
     *
     * @param Definition $definition
     * @param \Throwable $exception
     * @return $this
     */
    public function printException(Definition $definition, $exception);
}
