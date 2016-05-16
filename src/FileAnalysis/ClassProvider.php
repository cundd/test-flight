<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:05
 */

namespace Cundd\TestFlight\FileAnalysis;

/**
 * Provider for class names of classes containing test methods
 */
class ClassProvider
{
    /**
     * @param FileInterface[] $files
     * @return array
     */
    public function findClassesInFiles(array $files)
    {
        $classes = [];
        foreach ($files as $file) {
            if ($this->isPhpFile($file)) {
                // TODO: Check for duplicate class names
                $classesInFile = $this->getClassFromFile($file);
                $classes = array_merge(
                    $classes,
                    $this->buildDictionaryWithClassesAndFile($classesInFile, $file)
                );
            }
        }

        return $classes;
    }

    /**
     * @param FileInterface $file
     * @return bool
     */
    private function isPhpFile(FileInterface $file)
    {
        return pathinfo($file->getPath(), PATHINFO_EXTENSION) === 'php';
    }

    /**
     * @param string[] $classes
     * @param FileInterface $file
     * @return array
     */
    private function buildDictionaryWithClassesAndFile(array $classes, FileInterface $file)
    {
        $dictionary = array_flip($classes);
        array_walk(
            $dictionary,
            function (&$value) use ($file) {
                $value = $file;
            }
        );

        return $dictionary;
    }

    /**
     * @param FileInterface $file
     * @return string[]
     */
    private function getClassFromFile(FileInterface $file)
    {
        $classes = [];
        $tokens = token_get_all($file->getContents());
        $count = count($tokens);
        $namespace = '';
        for ($i = 2; $i < $count; $i++) {
            // Detect the namespace
            if ($tokens[$i - 2][0] === T_NAMESPACE
                && $tokens[$i - 1][0] === T_WHITESPACE
                && $tokens[$i][0] === T_STRING
            ) {
                while ($tokens[$i][0] === T_STRING || $tokens[$i][0] === T_NS_SEPARATOR) {
                    $namespace .= $tokens[$i][1];
                    $i += 1;
                }
            }

            if ($tokens[$i - 2][0] === T_CLASS
                && $tokens[$i - 1][0] === T_WHITESPACE
                && $tokens[$i][0] === T_STRING
            ) {
                $classes[] = $tokens[$i][1];
            }
        }


        if ($namespace) {
            return array_map(
                function ($class) use ($namespace) {
                    return "$namespace\\$class";
                },
                $classes
            );
        }

        return $classes;
    }

    /**
     * @test
     */
    protected function findClassesInFilesTest()
    {
        $classes = $this->findClassesInFiles([new File(__FILE__)]);
        test_flight_assert(1 === count($classes));
        test_flight_assert(__CLASS__ === key($classes));

        $classes = $this->findClassesInFiles([new File(__DIR__ . '/../functions.php')]);
        test_flight_assert(0 === count($classes));
    }
}
