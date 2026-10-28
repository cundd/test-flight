<?php

declare(strict_types=1);

namespace Cundd\TestFlight;

interface CodeExtractorInterface
{
    /**
     * Extract the example code from the doc comment
     */
    public function getCodeFromDocComment(string $docComment): string;

    /**
     * Extract the example code from the documentation file
     *
     * @return string[]
     */
    public function getCodeFromDocumentation(string $fileContent): array;
}
