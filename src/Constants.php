<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 13:34
 */

namespace Cundd\TestFlight;


interface Constants
{
    const VERSION = '0.2.6';

    const TEST_KEYWORD = '@test';
    const EXAMPLE_KEYWORD = '@example';
    const CODE_KEYWORD = '<code>';
    const MARKDOWN_PHP_CODE_KEYWORD = '```php';

    const TEST_TYPE_DOCUMENTATION = 'documentation';
    const TEST_TYPE_DOC_COMMENT = 'doccomment';
    const TEST_TYPE_METHOD = 'method';
}