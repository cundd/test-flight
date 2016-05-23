<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 28/04/16
 * Time: 20:52
 */

namespace Cundd\TestFlight\TestRunner;


use Cundd\TestFlight\Context\ContextInterface;
use Cundd\TestFlight\Definition\CodeDefinitionInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;


/**
 * Test Runner for code string tests
 */
class CodeTestRunner extends AbstractTestRunner
{
    /**
     * @param CodeDefinitionInterface|DefinitionInterface $definition
     * @param ContextInterface                            $context
     */
    protected function performTest(DefinitionInterface $definition, ContextInterface $context)
    {
        $preprocessedCode = $definition->getPreProcessedCode();
        if (!$preprocessedCode || $preprocessedCode === ';') {
            $this->printer->warn('No test code given');
        }
        $this->evaluate($preprocessedCode, $context);
    }

    /**
     * @param string           $code
     * @param ContextInterface $context
     * @return mixed
     */
    private function evaluate($code, ContextInterface $context)
    {
        return call_user_func(
            function () use ($code, $context) {
                extract($context->getVariables());

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
