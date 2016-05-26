<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 16/04/16
 * Time: 22:34
 */
declare(strict_types = 1);

namespace Cundd\TestFlight\Definition;

use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * Definition of a test defined as a block of code
 */
class DocumentationCodeDefinition extends AbstractCodeDefinition
{
    /**
     * @param string        $code
     * @param FileInterface $file
     */
    public function __construct(string $code, FileInterface $file)
    {
        $this->code = $code;
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return '';
    }

    /**
     * Returns a description for the output
     *
     * @return string
     */
    public function getDescription(): string
    {
        $fileName = pathinfo($this->getFilePath(), PATHINFO_FILENAME);
        $testName = $fileName;

        if ($testName[0] === '_') {
            $testName = substr($testName, 1);
        }
        if (strtoupper($testName) === 'README') {
            $testName = basename(dirname($this->getFilePath())).': Readme';
        }

        return sprintf(
            '%s "%s"',
            $this->getType(),
            str_replace(
                '  ',
                ' ',
                ucwords(
                    ltrim(strtolower(preg_replace('/[A-Z]/', ' $0', $testName)))
                )
            )
        );
    }

    /**
     * Returns the descriptive type of the definition
     *
     * @return string
     */
    public function getType(): string
    {
        return 'Documentation test';
    }

    /**
     * @test
     */
    protected static function getPreProcessedCodeTest()
    {
        $file = new \Cundd\TestFlight\FileAnalysis\File('some/path');
        $originalCode = '';
        $definition = new DocumentationCodeDefinition($originalCode, $file);
        test_flight_assert_same('', $definition->getPreProcessedCode());

        $originalCode = '__FILE__';
        $definition = new DocumentationCodeDefinition($originalCode, $file);
        test_flight_assert_same("'some/path'", $definition->getPreProcessedCode());

        $originalCode = '__DIR__';
        $definition = new DocumentationCodeDefinition($originalCode, $file);
        test_flight_assert_same("'some'", $definition->getPreProcessedCode());

        $originalCode = 'assert(true)';
        $definition = new DocumentationCodeDefinition($originalCode, $file);
        test_flight_assert_same('test_flight_assert(true)', $definition->getPreProcessedCode());

        $originalCode = '//assert(true)';
        $definition = new DocumentationCodeDefinition($originalCode, $file);
        test_flight_assert_same('//test_flight_assert(true)', $definition->getPreProcessedCode());

        $originalCode = '
$someValue = true;
assert($someValue)';
        $definition = new DocumentationCodeDefinition($originalCode, $file);
        test_flight_assert_same(
            '
$someValue = true;
test_flight_assert($someValue)',
            $definition->getPreProcessedCode()
        );

        $originalCode = '\Cundd\TestFlight\Assert::assertSame(true)';
        $definition = new DocumentationCodeDefinition($originalCode, $file);
        test_flight_assert_same('\Cundd\TestFlight\Assert::assertSame(true)', $definition->getPreProcessedCode());
    }
}
