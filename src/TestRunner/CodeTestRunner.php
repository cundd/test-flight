<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28/04/16
 * Time: 20:52
 */

namespace Cundd\TestFlight\TestRunner;


use Cundd\TestFlight\Definition\CodeDefinition;
use Cundd\TestFlight\Definition\DefinitionInterface;


/**
 * Test Runner for code string tests
 */
class CodeTestRunner extends AbstractTestRunner
{
    /**
     * @param CodeDefinition|DefinitionInterface $definition
     * @return void
     */
    protected function performTest(DefinitionInterface $definition)
    {
        $this->evaluate($this->preprocessCode($definition));
    }

    /**
     * @param string $code
     * @return mixed
     */
    private function evaluate(string $code)
    {
        return call_user_func(
            function () use ($code) {
                return eval($code);
            }
        );
    }

    /**
     * @param CodeDefinition $definition
     * @return string
     */
    private function preprocessCode(CodeDefinition $definition): string
    {
        $code = $definition->getCode();

        $filePath = $definition->getFilePath();
        $code = str_replace(
            '__FILE__',
            "'".$filePath."'",
            $code
        );
        $code = str_replace(
            '__DIR__',
            "'".dirname($filePath)."'",
            $code
        );
        $code = str_replace(
            '__CLASS__',
            "'".$definition->getClassName()."'",
            $code
        );
        $code = preg_replace(
            '/[^\w:]assert\(/',
            'test_flight_assert(',
            $code
        );

        return $code;
    }

//    private function checkSyntax(CodeDefinition $definition)
//    {
//        $tempFilePath = tempnam(sys_get_temp_dir(), 'test-flight');
//        if (!is_writable($tempFilePath) && !is_writable(dirname($tempFilePath))) {
//            throw FileNotReadableException::exceptionForFile($tempFilePath);
//        }
//
//        file_put_contents($tempFilePath, '<?php ' . $definition->getCode() . ' ?' . '>');
//    }
}
