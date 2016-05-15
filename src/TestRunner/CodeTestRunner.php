<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28/04/16
 * Time: 20:52
 */

namespace Cundd\TestFlight\TestRunner;


use Cundd\TestFlight\Definition\CodeDefinitionInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;


/**
 * Test Runner for code string tests
 */
class CodeTestRunner extends AbstractTestRunner
{
    /**
     * @param CodeDefinitionInterface|DefinitionInterface $definition
     * @return void
     */
    protected function performTest(DefinitionInterface $definition)
    {
        $preprocessedCode = $definition->getPreProcessedCode();
        if (!$preprocessedCode || $preprocessedCode === ';') {
            $this->printer->warn('No test code given');
        }
        $this->evaluate($preprocessedCode);
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
