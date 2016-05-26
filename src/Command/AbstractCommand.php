<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 26/05/16
 * Time: 13:07
 */

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
    /**
     * @var ClassLoader
     */
    protected $classLoader;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var PrinterInterface
     */
    protected $printer;

    /**
     * @var ExceptionPrinterInterface
     */
    protected $exceptionPrinter;

    /**
     * @var ConfigurationProviderInterface
     */
    protected $configurationProvider;

    /**
     * AbstractCommand constructor.
     *
     * @param ConfigurationProviderInterface $configurationProvider
     * @param ObjectManager                  $objectManager
     * @param ClassLoader                    $classLoader
     * @param PrinterInterface               $printer
     * @param ExceptionPrinterInterface      $exceptionPrinter
     */
    public function __construct(
        ConfigurationProviderInterface $configurationProvider,
        ObjectManager $objectManager,
        ClassLoader $classLoader,
        PrinterInterface $printer,
        ExceptionPrinterInterface $exceptionPrinter
    ) {
        $this->configurationProvider = $configurationProvider;
        $this->objectManager = $objectManager;
        $this->classLoader = $classLoader;
        $this->printer = $printer;
        $this->exceptionPrinter = $exceptionPrinter;
    }
}
