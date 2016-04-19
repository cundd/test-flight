<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:05
 */

namespace Cundd\TestFlight\FileAnalysis;


use Cundd\TestFlight\Constants;
use Cundd\TestFlight\Exception\FileNotExistsException;
use Cundd\TestFlight\Exception\FileNotReadableException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class FileProvider
{
    /**
     * @param string $directory
     * @return File[]
     */
    public function findInDirectory($directory)
    {
        $directory = $this->validateDirectory($directory);
        $directoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $regexIterator = new RegexIterator($directoryIterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

        $fileIncludingTests = [];
        foreach ($regexIterator as $pathCollection) {
            $file = new File($pathCollection[0]);
            if ($file->containsKeyword(Constants::TEST_KEYWORD)) {
                $fileIncludingTests[] = $file;
            }
        }

        return $fileIncludingTests;
    }

    /**
     * @param string $directory
     * @return string
     */
    private function validateDirectory($directory)
    {
        $directory = realpath($directory) ?: $directory;

        if (!file_exists($directory)) {
            throw FileNotExistsException::exceptionForFile($directory);
        }
        if (!is_readable($directory)) {
            throw FileNotReadableException::exceptionForFile($directory);
        }

        return $directory;
    }

    /**
     * @test
     */
    protected function findFilesWithTestsInDirectoryTest()
    {
        // 3 <= 2 files with @test + 1 constants interface
        $files = $this->findInDirectory(__DIR__.'/../');
        assert(count($files) === 3);
        assert($files[0] instanceof File);

        $oneFileIsThisFileClosure = function (File $file) {
            return $file->getPath() === __FILE__;
        };
        assert(1 === count(array_filter($files, $oneFileIsThisFileClosure)));
    }
}
