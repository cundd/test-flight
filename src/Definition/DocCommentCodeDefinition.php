<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Definition;

use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * Definition of a test defined as Doc Comment
 */
class DocCommentCodeDefinition extends AbstractCodeDefinition
{
    public function __construct(
        private readonly string $className,
        string $code,
        FileInterface $file,
        private readonly string $relatedMethodName,
    ) {
        $this->code = $code;
        $this->file = $file;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getRelatedMethodName(): string
    {
        return $this->relatedMethodName;
    }

    /**
     * Returns a description for the output
     */
    public function getDescription(): string
    {
        return sprintf(
            '%s "%s"',
            $this->getType(),
            ucwords(
                ltrim(strtolower(preg_replace('/[A-Z]/', ' $0', $this->getRelatedMethodName())))
            )
        );
    }

    /**
     * Returns the descriptive type of the definition
     */
    public function getType(): string
    {
        return 'DocComment test';
    }
}
