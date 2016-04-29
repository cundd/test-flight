<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 29/04/16
 * Time: 21:23
 */

namespace Cundd\TestFlight\Cli;

/**
 * CLI option parser
 */
class OptionParser
{
    /**
     * Parse CLI arguments
     *
     * @example
     *  $arguments = ['the/path', '--type', 'doccomment'];
     *  $parser = new \Cundd\TestFlight\Cli\OptionParser();
     *  $parsedArguments = $parser->parse($arguments);
     *  test_flight_assert(is_array($parsedArguments));
     *  test_flight_assert($parsedArguments === ['path' => 'the/path', 'type' => 'doccomment']);
     *
     * @param string[] $arguments
     * @return array
     * @throws \Exception
     */
    public function parse(array $arguments)
    {
        $preparedArguments = [];
        $argumentsLength = count($arguments);

        for ($i = 0; $i < $argumentsLength; $i++) {
            $currentArgument = $arguments[$i];
            if (substr($currentArgument, 0, 2) === '--') {
                $name = substr($currentArgument, 2);

                if (strpos($name, '=') !== false) {
                    list($name, $value) = explode('=', $name);
                } elseif ($i + 1 < $argumentsLength) {
                    $i += 1;

                    $value = $arguments[$i];
                } else {
                    throw new \Exception('Invalid arguments');
                }
                $preparedArguments[$name] = $value;
            } else {
                $preparedArguments['path'] = $currentArgument;
            }
        }

        return $preparedArguments;
    }
}