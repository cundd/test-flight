<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/05/16
 * Time: 14:44
 */

namespace Cundd\TestFlight\Autoload;


/**
 * Class to search for an autoloader for the current project
 */
class Finder
{
    /**
     * @var string[]
     */
    private $knownAutoloaderPaths = [
        'vendor/autoload.php',
    ];

    /**
     * Find the best autoloader for the current project
     *
     * @example
     *  $finder = new \Cundd\TestFlight\Autoload\Finder();
     *  $foundAutoloaderPath = $finder->find(__DIR__);
     *  assert(substr($foundAutoloaderPath, -19) === 'vendor/autoload.php');
     * @param string $basePath
     * @return string
     */
    public function find($basePath)
    {
        if (is_file($basePath)) {
            $basePath = dirname($basePath);
        } elseif (!is_dir($basePath)) {
            throw new \InvalidArgumentException('Base path must be either a file path or directory');
        }

        $basePath = realpath($basePath) ?: $basePath;

        do {
            $autoloaderPath = $this->containsKnownAutoloader($basePath);
            if ($autoloaderPath !== '') {
                return $autoloaderPath;
            }
            $basePath = dirname($basePath);
        } while ($basePath && $basePath !== '/');

        return '';
    }

    /**
     * @param $path
     * @return string
     */
    private function containsKnownAutoloader($path)
    {
        foreach ($this->knownAutoloaderPaths as $autoloaderPath) {
            if (file_exists($path.'/'.$autoloaderPath)) {
                return $path.'/'.$autoloaderPath;
            }
        }

        return '';
    }
}
