<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 20:53
 */

namespace Cundd\TestFlight;


use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * A custom class loader which uses the file interface
 */
class ClassLoader
{
    /**
     * @param string        $className
     * @param FileInterface $file
     */
    public function loadClass($className, FileInterface $file)
    {
        if (!class_exists($className)) {
            include_once $file->getPath();
        }
    }
}