<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Result;

use Cundd\TestFlight\Exception\ResultException;
use Cundd\TestFlight\Result;

/**
 * @template T
 *
 * @extends Result<T,never>
 */
final class Ok extends Result
{
    /**
     * @param T $value
     */
    public function __construct(private readonly mixed $value)
    {
    }

    public function isOk(): bool
    {
        return true;
    }

    public function unwrap(): mixed
    {
        return $this->value;
    }

    public function unwrapErr(): mixed
    {
        throw new ResultException('Called `unwrapErr` on Ok');
    }
}
