<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 18/04/16
 * Time: 20:53
 */

namespace Cundd\TestFlight;


use Cundd\TestFlight\FileAnalysis\File;

class ClassLoader
{
    /**
     * @param string $className
     * @param File   $file
     */
    public function loadClass($className, File $file)
    {
        if (!class_exists($className)) {
            include_once $file->getPath();
        }
    }
}