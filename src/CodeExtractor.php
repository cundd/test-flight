<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 27/04/16
 * Time: 21:37
 */

namespace Cundd\TestFlight;

/**
 * Class to read Code from the given doc comment
 *
 * @package Cundd\TestFlight
 */
class CodeExtractor
{
    const DOC_COMMENT_REGEX = '\s+([^@]*)[.\s@]+';
    const DOCUMENTATION_REGEX = '\n([^`]*)```';

    /**
     * Extract the example code from the doc comment
     *
     * @param string $docComment
     * @return string
     */
    public function getCodeFromDocComment(string $docComment): string
    {
        $start = strpos($docComment, Constants::EXAMPLE_KEYWORD);
        $code = substr($docComment, $start);

        $regularExpression = '!'.Constants::EXAMPLE_KEYWORD.self::DOC_COMMENT_REGEX.'!';
        if (!preg_match($regularExpression, $code, $matches)) {
            return '';
        }

        $codeLines = array_filter(
            array_map(
                function ($line) {
                    return ltrim($line, " \t\n\r\0\x0B*");
                },
                explode("\n", $matches[1])
            )
        );

        return implode("\n", $codeLines).';';
    }

    /**
     * Extract the example code from the documentation file
     *
     * @param string $fileContent
     * @return string[]
     */
    public function getCodeFromDocumentation(string $fileContent): array
    {
        $start = strpos($fileContent, Constants::MARKDOWN_PHP_CODE_KEYWORD);
        $code = substr($fileContent, $start);

        $regularExpression = '!'.Constants::MARKDOWN_PHP_CODE_KEYWORD.self::DOCUMENTATION_REGEX.'!';
        if (!preg_match_all($regularExpression, $code, $matches)) {
            return [];
        }

        return array_map(
            function ($line) {
                return trim($line);
            },
            $matches[1]
        );
    }

    /**
     * @test
     */
    protected function getCodeFromDocCommentTest()
    {
        $docComment = '/**
     * Returns the file\'s path
     *
     * @example assert(__FILE__, (new File(__FILE__))->getPath())
     *
     * @return string
     */';
        $code = $this->getCodeFromDocComment($docComment);
        test_flight_assert(is_string($code));
        test_flight_assert('assert(__FILE__, (new File(__FILE__))->getPath());' === $code);

        $docComment = '/**
     * Returns the file\'s path
     *
     * @example assert(__FILE__, (new File(__FILE__))->getPath())
     * @return string
     */';
        $code = $this->getCodeFromDocComment($docComment);
        test_flight_assert(is_string($code));
        test_flight_assert('assert(__FILE__, (new File(__FILE__))->getPath());' === $code);
        $docComment = '/**
     * Returns the file\'s path
     *
     * @example assert(__FILE__, (new File(__FILE__))->getPath())
     * @param string $xy
     * @return string
     */';
        $code = $this->getCodeFromDocComment($docComment);
        test_flight_assert(is_string($code));
        test_flight_assert('assert(__FILE__, (new File(__FILE__))->getPath());' === $code);
    }

    /**
     * @test
     */
    protected function getCodeFromDocumentationTest()
    {
        $documentation = '
Documentation
=============

```php
assert(true);
```

```php
assert(true);

assert(1 < 2);
```
';
        $codeSamples = $this->getCodeFromDocumentation($documentation);
        test_flight_assert_type('array', $codeSamples);
        test_flight_assert_type('string', $codeSamples[0]);
        test_flight_assert_same('assert(true);', $codeSamples[0]);
        test_flight_assert_same('assert(true);

assert(1 < 2);', $codeSamples[1]);
    }
}
