<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Definition;

use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * Definition of a test defined as Doc Comment
 */
class DocCommentCodeDefinition extends AbstractCodeDefinition
{
    /**
     * @param class-string $className
     */
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

    public function getDescription(): string
    {
        return sprintf(
            '%s "%s"',
            $this->getType(),
            ucwords(
                ltrim(strtolower((string) preg_replace(
                    '/[A-Z]/',
                    ' $0',
                    $this->getRelatedMethodName()
                )))
            )
        );
    }

    public function getType(): string
    {
        return 'DocComment test';
    }
}
