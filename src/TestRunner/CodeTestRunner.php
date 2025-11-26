<?php

declare(strict_types=1);

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
     */
    protected function performTest(
        DefinitionInterface $definition,
        ContextInterface $context,
    ): void {
        $preprocessedCode = $definition->getPreProcessedCode();
        if (!$preprocessedCode || ';' === $preprocessedCode) {
            $this->printer->warn('No test code given');
        }
        $this->evaluate($preprocessedCode, $context);
    }

    private function evaluate(string $code, ContextInterface $context): mixed
    {
        return call_user_func(
            function () use ($code, $context) {
                extract($context->getVariables());

                return eval($code);
            }
        );
    }
}
