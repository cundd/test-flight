<?php

declare(strict_types=1);

namespace Cundd\TestFlight;

use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * A custom class loader which uses the file interface
 */
class ClassLoader
{
    /**
     * @param class-string $className
     */
    public function loadClass(string $className, FileInterface $file): void
    {
        if (!class_exists($className)) {
            include_once $file->getPath();
        }
    }
}
