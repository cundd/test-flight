<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Command;

use Cundd\TestFlight\ClassLoader;
use Cundd\TestFlight\Configuration\ConfigurationProviderInterface;
use Cundd\TestFlight\ObjectManager;
use Cundd\TestFlight\Output\ExceptionPrinterInterface;
use Cundd\TestFlight\Output\PrinterInterface;

/**
 * Abstract Command class
 */
abstract class AbstractCommand implements CommandInterface
{
    public function __construct(
        protected readonly ConfigurationProviderInterface $configurationProvider,
        protected readonly ObjectManager $objectManager,
        protected readonly ClassLoader $classLoader,
        protected readonly PrinterInterface $printer,
        protected readonly ExceptionPrinterInterface $exceptionPrinter,
    ) {
    }
}
