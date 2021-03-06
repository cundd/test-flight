<?php
declare(strict_types=1);

namespace Cundd\TestFlight\Output;

use Cundd\TestFlight\Definition\DefinitionInterface;


/**
 * Special printer interface for exception output
 */
interface ExceptionPrinterInterface extends PrinterInterface
{
    /**
     * Prints the exception for the given test definition
     *
     * @param DefinitionInterface $definition
     * @param \Throwable          $exception
     * @return $this
     */
    public function printException(DefinitionInterface $definition, $exception);
}
