<?php

declare(strict_types=1);

namespace Cundd\TestFlight;

use Cundd\TestFlight\Result\Err;
use Cundd\TestFlight\Result\Ok;
use Throwable;

final class ErrorBoundary
{
    /**
     * @template T
     *
     * @param callable(mixed|null $first, mixed ...$arguments): T $callable
     *
     * @return Result<T, Throwable>
     */
    public function execute(callable $callable, mixed ...$arguments): Result
    {
        try {
            return new Ok($callable(...$arguments));
        } catch (Throwable $e) {
            return new Err($e);
        }
    }
}
