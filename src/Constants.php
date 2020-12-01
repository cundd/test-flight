<?php
declare(strict_types=1);


namespace Cundd\TestFlight;


interface Constants
{
    const VERSION = '0.3.0';

    const TEST_KEYWORD = '@test';
    const EXAMPLE_KEYWORD = '@example';
    const CODE_KEYWORD = '<code>';
    const MARKDOWN_PHP_CODE_KEYWORD = '```php';

    const TEST_TYPE_DOCUMENTATION = 'documentation';
    const TEST_TYPE_DOC_COMMENT = 'doccomment';
    const TEST_TYPE_METHOD = 'method';
}
