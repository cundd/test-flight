<?php
declare(strict_types=1);


namespace Cundd\TestFlight\Definition;


use Cundd\TestFlight\FileAnalysis\FileInterface;

/**
 * Abstract base class for Code based definitions
 */
abstract class AbstractCodeDefinition implements CodeDefinitionInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @var FileInterface
     */
    protected $file;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return FileInterface
     */
    public function getFile(): FileInterface
    {
        return $this->file;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->getFile()->getPath();
    }

    /**
     * Returns the pre-processed code
     *
     * @return string
     */
    public function getPreProcessedCode(): string
    {
        $code = $this->getCode();

        $filePath = $this->getFilePath();
        $code = str_replace(
            '__FILE__',
            "'" . $filePath . "'",
            $code
        );
        $code = str_replace(
            '__DIR__',
            "'" . dirname($filePath) . "'",
            $code
        );
        $code = preg_replace(
            '/([^\w:])(__CLASS__)\(/',
            '$1' . $this->getClassName() . '(',
            $code
        );
        $code = str_replace(
            '__CLASS__',
            "'" . $this->getClassName() . "'",
            $code
        );
        $code = preg_replace(
            '/^(assert)\(/',
            'test_flight_assert(',
            $code
        );
        $code = preg_replace(
            '/([^\w:])(assert)\(/',
            '$1test_flight_assert(',
            $code
        );

        return $code;
    }
}
