<?php

declare(strict_types=1);

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
 *
 * <code>
 *  $files = (new \Cundd\TestFlight\FileAnalysis\FileProvider())->findMatchingFiles(__DIR__.'/../');
 *  assert($files[0] instanceof \Cundd\TestFlight\FileAnalysis\FileInterface);
 * </code>
 */
class FileProvider
{
    private array $fileSuffix = [
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

    private function findMatchingFilesInDirectory(string $path): array
    {
        $directoryIterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $regexIterator = new RegexIterator(
            $directoryIterator,
            sprintf('/^.+\.(%s)$/i', implode('|', $this->fileSuffix)),
            RecursiveRegexIterator::GET_MATCH
        );

        return array_filter(
            array_column(
                iterator_to_array($regexIterator),
                0
            ),
            'is_file'
        );
    }

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

    private function getFileIncludesTest(FileInterface $file): bool
    {
        return $file->containsKeyword(Constants::TEST_KEYWORD)
            || $file->containsKeyword(Constants::EXAMPLE_KEYWORD)
            || $file->containsKeyword(Constants::CODE_KEYWORD)
            || $file->containsKeyword(Constants::MARKDOWN_PHP_CODE_KEYWORD);
    }

    /**
     * @test
     */
    protected function findMatchingFilesTest(): void
    {
        $expectedNumberOfFiles = 4;
        $files = $this->findMatchingFiles(__DIR__);
        assert(
            count($files) === $expectedNumberOfFiles,
            sprintf('Expected %d test files, found %d', $expectedNumberOfFiles, count($files))
        );
        assert($files[0] instanceof File);

        $oneFileIsThisFileClosure = function (FileInterface $file) {
            return __FILE__ === $file->getPath();
        };
        assert(1 === count(array_filter($files, $oneFileIsThisFileClosure)));
    }
}
