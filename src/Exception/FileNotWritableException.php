<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:07
 */

namespace Cundd\TestFlight\Exception;


class FileNotWritableException extends FileException
{
    /**
     * @param string          $filePath
     * @param int             $code
     * @param \Exception|null $previous
     * @return static
     */
    public static function exceptionForFile($filePath, $code = 0, \Exception $previous = null)
    {
        return new static(sprintf('File %s is not writable', $filePath), $code, $previous);
    }
}