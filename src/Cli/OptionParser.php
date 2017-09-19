<?php
declare(strict_types=1);


namespace Cundd\TestFlight\Cli;

/**
 * CLI option parser
 */
class OptionParser
{
    /**
     * Parse CLI arguments
     *
     * <code>
     *  $arguments = ['path/to/cli-script', 'the/path', '--type', 'doccomment', '-v', '--verbose', '--list', ''];
     *  $parser = new \Cundd\TestFlight\Cli\OptionParser();
     *  $parsedArguments = $parser->parse($arguments);
     *  test_flight_assert(is_array($parsedArguments));
     *  test_flight_assert_same([
     *      'path' => 'the/path',
     *      'type' => 'doccomment',
     *      'v' => true,
     *      'verbose' => true,
     *      'list' => '',
     *  ], $parsedArguments);
     * </code>
     *
     * @param string[] $arguments
     * @return array
     * @throws \Exception
     */
    public function parse(array $arguments)
    {
        $preparedArguments = [];
        $argumentsLength = count($arguments);

        for ($i = 1; $i < $argumentsLength; $i++) {
            $currentArgument = $arguments[$i];
            if (substr($currentArgument, 0, 2) === '--') { // Value option
                $name = substr($currentArgument, 2);

                if (strpos($name, '=') !== false) {
                    list($name, $value) = explode('=', $name);
                } elseif ($this->nextElementIsValueElement($arguments, $i, $argumentsLength)) {
                    $i += 1;

                    $value = $arguments[$i];
                } else {
                    $value = true;
                }
                $preparedArguments[$name] = $value;
            } elseif ($currentArgument[0] === '-') { // Flag option
                $preparedArguments[substr($currentArgument, 1)] = true;
            } else {
                $preparedArguments['path'] = $currentArgument;
            }
        }

        return $preparedArguments;
    }

    /**
     * @param array $arguments
     * @param       $currentIndex
     * @param       $argumentsLength
     * @return bool
     */
    private function nextElementIsValueElement(array $arguments, $currentIndex, $argumentsLength)
    {
        return $currentIndex + 1 < $argumentsLength
            && is_string($arguments[$currentIndex + 1])
            && (
                strlen($arguments[$currentIndex + 1]) === 0 || $arguments[$currentIndex + 1][0] !== '-'
            );
    }
}