<?php

declare(strict_types=1);

namespace Cundd\TestFlight\TestRunner;

use Cundd\TestFlight\Context\ContextInterface;
use Cundd\TestFlight\Definition\CodeDefinitionInterface;
use Cundd\TestFlight\Definition\DefinitionInterface;
use Cundd\TestFlight\ErrorBoundary;
use Cundd\TestFlight\Exception\EvalError;
use Cundd\TestFlight\Result;
use Cundd\TestFlight\Result\Ok;
use Throwable;

/**
 * Test Runner for code string tests
 */
class CodeTestRunner extends AbstractTestRunner
{
    /**
     * @param CodeDefinitionInterface&DefinitionInterface $definition
     */
    protected function performTest(
        DefinitionInterface $definition,
        ContextInterface $context,
    ): Result {
        $preprocessedCode = $definition->getPreProcessedCode();
        if (!$preprocessedCode || ';' === $preprocessedCode) {
            $this->printer->warn('No test code given');

            return new Ok(null);
        }

        return $this->evaluate($preprocessedCode, $definition, $context);
    }

    /**
     * @return Result<mixed, Throwable>
     */
    private function evaluate(
        string $code,
        DefinitionInterface $definition,
        ContextInterface $context,
    ): Result {
        return (new ErrorBoundary())->execute(
            function () use ($code, $definition, $context) {
                extract($context->getVariables());

                try {
                    return eval($code);
                } catch (Throwable $e) {
                    throw new EvalError(
                        $definition->getFile(),
                        $e->getMessage(),
                        $e->getCode(),
                        $e
                    );
                }
            }
        );
    }
}
