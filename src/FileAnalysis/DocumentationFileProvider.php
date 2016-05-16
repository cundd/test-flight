<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 07/05/16
 * Time: 13:16
 */

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
     * @return array
     */
    public function findDocumentationFiles(array $files)
    {
        return array_filter($files, function(FileInterface $file) {
            return substr($file->getPath(), -4) !== '.php';
        });
    }
}
