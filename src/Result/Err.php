<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Result;

use Cundd\TestFlight\Exception\ResultException;
use Cundd\TestFlight\Result;

/**
 * @template E
 *
 * @extends Result<never,E>
 */
final class Err extends Result
{
    /**
     * @param E $value
     */
    public function __construct(private readonly mixed $value)
    {
    }

    public function isOk(): bool
    {
        return false;
    }

    public function unwrap(): mixed
    {
        throw new ResultException('Called `unwrap` on Err');
    }

    public function unwrapErr(): mixed
    {
        return $this->value;
    }
}
