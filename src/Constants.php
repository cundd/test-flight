<?php

declare(strict_types=1);

namespace Cundd\TestFlight;

interface Constants
{
    public const VERSION = '0.3.0';

    public const TEST_KEYWORD = '@test';
    public const EXAMPLE_KEYWORD = '@example';
    public const CODE_KEYWORD = '<code>';
    public const MARKDOWN_PHP_CODE_KEYWORD = '```php';

    public const TEST_TYPE_DOCUMENTATION = 'documentation';
    public const TEST_TYPE_DOC_COMMENT = 'doccomment';
    public const TEST_TYPE_METHOD = 'method';
}
