<?php
declare(strict_types=1);


namespace Cundd\TestFlight\Output;

use Cundd\TestFlight\Cli\WindowHelper;

/**
 * Formatter for PHP code
 */
class CodeFormatter implements ColorInterface
{
    /**
     * @var int
     */
    private $numberOfLines;

    /**
     * @var int
     */
    private $gutterWidth;

    /**
     * @var WindowHelper
     */
    private $cliWindowHelper;

    /**
     * @var bool
     */
    private $enableColors;

    /**
     * CodeFormatter constructor.
     *
     * @param WindowHelper $cliWindowHelper
     */
    public function __construct(WindowHelper $cliWindowHelper)
    {
        $this->cliWindowHelper = $cliWindowHelper;
    }

    /**
     * Formats the given code
     *
     * @example
     *  $formatter = new \Cundd\TestFlight\Output\CodeFormatter(
     *      new \Cundd\TestFlight\Cli\WindowHelper()
     *  );
     *  test_flight_assert_same('1 line 1', trim($formatter->formatCode('line 1', false)));
     * @param string $code
     * @param bool   $enableColors
     * @return string
     */
    public function formatCode(string $code, $enableColors): string
    {
        $block = [];
        $codeLines = explode("\n", $code);

        $this->enableColors = (bool)$enableColors;
        $this->numberOfLines = count($codeLines);
        $this->gutterWidth = strlen((string)$this->numberOfLines) + 1;

        $i = 0;
        foreach ($codeLines as $lineNumber => $line) {
            $i += 1;
            $this->prepareCodeLine($line, $block, $i);
        }

        return "\n" . $this->colorize(
                self::NORMAL . self::LIGHT_GRAY_BACKGROUND . self::WHITE,
                implode("\n", $block),
                self::RED
            );
    }

    /**
     * @param string   $line
     * @param string[] $block
     * @param int      $lineNumber
     */
    private function prepareCodeLine(string $line, array &$block, int $lineNumber)
    {
        $width = $this->cliWindowHelper->getWidth() - $this->gutterWidth;
        $line = str_replace("\t", '    ', $line);

        $gutter = str_pad((string)$lineNumber, $this->gutterWidth - 1, ' ', STR_PAD_LEFT) . ' ';

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

    /**
     * @param string $startColor
     * @param string $text
     * @param string $endColor
     * @return string
     */
    private function colorize(string $startColor, string $text, string $endColor = ''): string
    {
        if ($this->enableColors) {
            return $startColor . $text . $endColor;
        }

        return $text;
    }

    /**
     * @param $line
     * @return string
     */
    private function colorizeLine(string $line): string
    {
        return $this->colorize(self::NORMAL . self::LIGHT_GRAY_BACKGROUND . self::DARK_GRAY, $line);
    }

    /**
     * @param $line
     * @return string
     */
    private function colorizeGutter(string $line): string
    {
        return $this->colorize(self::NORMAL . self::LIGHT_GRAY_BACKGROUND . self::WHITE, $line);
    }
}
