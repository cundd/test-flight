<?php

declare(strict_types=1);

namespace Cundd\TestFlight\FileAnalysis;

/**
 * Provider for class names of classes containing test methods
 */
final class ClassProvider
{
    /**
     * @param FileInterface[] $files
     *
     * @return array<class-string,FileInterface> $classes
     */
    public function findClassesInFiles(array $files): array
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

    private function isPhpFile(FileInterface $file): bool
    {
        return 'php' === pathinfo($file->getPath(), PATHINFO_EXTENSION);
    }

    /**
     * @param class-string[] $classes
     *
     * @return array<class-string,FileInterface>
     */
    private function buildDictionaryWithClassesAndFile(
        array $classes,
        FileInterface $file,
    ): array {
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
     * @return class-string[]
     */
    private function getClassFromFile(FileInterface $file): array
    {
        $classes = [];
        $tokens = token_get_all($file->getContents());
        $count = count($tokens);
        $namespace = '';
        for ($i = 2; $i < $count; ++$i) {
            // Detect the namespace
            if (T_NAMESPACE === $tokens[$i - 2][0]
                && T_WHITESPACE === $tokens[$i - 1][0]
                && T_STRING === $tokens[$i][0]
            ) {
                while (T_STRING === $tokens[$i][0] || T_NS_SEPARATOR === $tokens[$i][0]) {
                    $namespace .= $tokens[$i][1];
                    ++$i;
                }
            }

            // PHP8.0+
            if (defined('T_NAME_QUALIFIED')) {
                if (T_NAMESPACE === $tokens[$i - 2][0]
                      && T_WHITESPACE === $tokens[$i - 1][0]
                      && (T_NAME_QUALIFIED === $tokens[$i][0])
                ) {
                    $namespace = $tokens[$i][1];
                }
            }

            if (T_CLASS === $tokens[$i - 2][0]
                && T_WHITESPACE === $tokens[$i - 1][0]
                && T_STRING === $tokens[$i][0]
            ) {
                $classes[] = $tokens[$i][1];
            }
        }

        if ($namespace) {
            // @phpstan-ignore return.type
            return array_map(
                fn ($class) => "$namespace\\$class",
                $classes
            );
        }

        // @phpstan-ignore return.type
        return $classes;
    }

    /**
     * @test
     *
     * @phpstan-ignore method.unused
     */
    private function findClassesInFilesTest(): void
    {
        $classes = $this->findClassesInFiles([new File(__FILE__)]);
        test_flight_assert(1 === count($classes));
        test_flight_assert(__CLASS__ === key($classes));

        $classes = $this->findClassesInFiles([
            new File(__DIR__ . '/../functions.php'),
        ]);
        test_flight_assert(0 === count($classes));
    }
}
