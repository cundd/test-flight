<?php

declare(strict_types=1);

namespace Cundd\TestFlight\FileAnalysis;

/**
 * Class to filter documentation files from all found files
 */
class DocumentationFileProvider
{
    /**
     * Returns the documentation files from the given files
     *
     * @param FileInterface[] $files
     */
    public function findDocumentationFiles(array $files): array
    {
        return array_filter(
            $files,
            function (FileInterface $file) {
                return '.php' !== substr($file->getPath(), -4);
            }
        );
    }
}
