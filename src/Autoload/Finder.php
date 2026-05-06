<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Autoload;

use InvalidArgumentException;

/**
 * Class to search for an autoloader for the current project
 */
final class Finder
{
    /**
     * @var string[]
     */
    private array $knownAutoloaderPaths = [
        'vendor/autoload.php',
    ];

    /**
     * Find the best autoloader for the current project
     *
     * @example
     *  $finder = new \Cundd\TestFlight\Autoload\Finder();
     *  $foundAutoloaderPath = $finder->find(__DIR__);
     *  assert(substr($foundAutoloaderPath, -19) === 'vendor/autoload.php');
     */
    public function find(string $basePath): string
    {
        if (is_file($basePath)) {
            $basePath = dirname($basePath);
        } elseif (!is_dir($basePath)) {
            throw new InvalidArgumentException(
                'Base path must be either a file path or directory'
            );
        }

        $basePath = realpath($basePath) ?: $basePath;

        do {
            $autoloaderPath = $this->containsKnownAutoloader($basePath);
            if ('' !== $autoloaderPath) {
                return $autoloaderPath;
            }
            $basePath = dirname($basePath);
        } while ($basePath && '/' !== $basePath);

        return '';
    }

    private function containsKnownAutoloader(string $path): string
    {
        foreach ($this->knownAutoloaderPaths as $autoloaderPath) {
            if (file_exists($path . '/' . $autoloaderPath)) {
                return $path . '/' . $autoloaderPath;
            }
        }

        return '';
    }
}
