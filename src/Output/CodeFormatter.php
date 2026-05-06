<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Output;

use Cundd\TestFlight\Cli\WindowHelper;

/**
 * Formatter for PHP code
 */
class CodeFormatter implements ColorInterface
{
    private int $numberOfLines;

    private int $gutterWidth;

    private bool $enableColors;

    public function __construct(private readonly WindowHelper $cliWindowHelper)
    {
    }

    /**
     * Formats the given code
     *
     * @example
     *  $formatter = new \Cundd\TestFlight\Output\CodeFormatter(
     *      new \Cundd\TestFlight\Cli\WindowHelper()
     *  );
     *  test_flight_assert_same('1 line 1', trim($formatter->formatCode('line 1', false)));
     *
     * @param bool $enableColors
     */
    public function formatCode(string $code, $enableColors): string
    {
        $block = [];
        $codeLines = explode("\n", $code);

        $this->enableColors = (bool) $enableColors;
        $this->numberOfLines = count($codeLines);
        $this->gutterWidth = strlen((string) $this->numberOfLines) + 1;

        $i = 0;
        foreach ($codeLines as $lineNumber => $line) {
            ++$i;
            $this->prepareCodeLine($line, $block, $i);
        }

        return "\n" . $this->colorize(
            self::NORMAL . self::LIGHT_GRAY_BACKGROUND . self::WHITE,
            implode("\n", $block),
            self::RED
        );
    }

    /**
     * @param string[] $block
     */
    private function prepareCodeLine(
        string $line,
        array &$block,
        int $lineNumber,
    ): void {
        $width = max(
            $this->cliWindowHelper->getWidth() - $this->gutterWidth,
            1
        );

        $line = str_replace("\t", '    ', $line);

        $gutter = str_pad(
            (string) $lineNumber,
            $this->gutterWidth - 1,
            ' ',
            STR_PAD_LEFT
        ) . ' ';

        foreach (str_split($line, $width) as $lineChunk) {
            if (strlen($lineChunk) <= $width) {
                $block[] = ''
                    . $this->colorizeGutter($gutter)
                    . $this->colorizeLine(str_pad($lineChunk, $width, ' '));
            } else {
                $block[] = ''
                    . $this->colorizeGutter($gutter)
                    . $this->colorizeLine($lineChunk);
            }
            $gutter = str_repeat(' ', $this->gutterWidth);
        }
    }

    private function colorize(
        string $startColor,
        string $text,
        string $endColor = '',
    ): string {
        if ($this->enableColors) {
            return $startColor . $text . $endColor;
        }

        return $text;
    }

    private function colorizeLine(string $line): string
    {
        return $this->colorize(
            self::NORMAL . self::LIGHT_GRAY_BACKGROUND . self::DARK_GRAY,
            $line
        );
    }

    private function colorizeGutter(string $line): string
    {
        return $this->colorize(
            self::NORMAL . self::LIGHT_GRAY_BACKGROUND . self::WHITE,
            $line
        );
    }
}
