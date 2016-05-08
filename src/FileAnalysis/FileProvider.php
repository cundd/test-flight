<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:05
 */
declare(strict_types = 1);

namespace Cundd\TestFlight\FileAnalysis;


use Cundd\TestFlight\Constants;
use Cundd\TestFlight\Exception\FileException;
use Cundd\TestFlight\Exception\FileNotExistsException;
use Cundd\TestFlight\Exception\FileNotReadableException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Class that scans for matching files
 */
class FileProvider
{
    private $fileSuffix = [
        'php',
        'md',
        'markdown',
    ];

    /**
     * Returns the matching files containing the test doc comment
     *
     * If path is a single file and it contains test it will be returned,
     * if it is a directory it will be scanned for files with test in
     * it's content
     *
     * @param string $path
     * @return FileInterface[]
     */
    public function findMatchingFiles(string $path): array
    {
        $path = $this->validatePath($path);

        if (is_dir($path)) {
            $pathCollection = $this->findMatchingFilesInDirectory($path);
        } elseif (is_file($path)) {
            $pathCollection = [$path];
        } else {
            throw new FileException(sprintf('Could not get file(s) for path %s', $path));
        }

        $fileIncludingTests = [];
        foreach ($pathCollection as $path) {
            $file = new File($path);
            if ($this->getFileIncludesTest($file)) {
                $fileIncludingTests[] = $file;
            }
        }

        return $fileIncludingTests;
    }

    /**
     * @param string $path
     * @return array
     */
    private function findMatchingFilesInDirectory(string $path): array
    {
        $directoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $regexIterator = new RegexIterator(
            $directoryIterator,
            sprintf('/^.+\.(%s)$/i', implode('|', $this->fileSuffix)),
            RecursiveRegexIterator::GET_MATCH
        );

        return array_filter(
            array_map(
                function ($pathCollection) {
                    return $pathCollection[0];
                },
                iterator_to_array($regexIterator)
            ),
            'is_file'
        );
    }

    /**
     * @param string $path
     * @return string
     */
    private function validatePath(string $path): string
    {
        $path = realpath($path) ?: $path;

        if (!file_exists($path)) {
            throw FileNotExistsException::exceptionForFile($path);
        }
        if (!is_readable($path)) {
            throw FileNotReadableException::exceptionForFile($path);
        }

        return $path;
    }

    /**
     * @param FileInterface $file
     * @return bool
     */
    private function getFileIncludesTest(FileInterface $file)
    {
        return $file->containsKeyword(Constants::TEST_KEYWORD)
        || $file->containsKeyword(Constants::EXAMPLE_KEYWORD)
        || $file->containsKeyword(Constants::MARKDOWN_PHP_CODE_KEYWORD);
    }

    /**
     * @test
     */
    protected function findMatchingFilesTest()
    {
        $expectedNumberOfFiles = 14;
        // x <= y files with @test + 1 constants interface
        $files = $this->findMatchingFiles(__DIR__.'/../');
        assert(
            count($files) === $expectedNumberOfFiles,
            sprintf('Expected %d test files, found %d', $expectedNumberOfFiles, count($files))
        );
        assert($files[0] instanceof File);

        $oneFileIsThisFileClosure = function (FileInterface $file) {
            return $file->getPath() === __FILE__;
        };
        assert(1 === count(array_filter($files, $oneFileIsThisFileClosure)));
    }
}
