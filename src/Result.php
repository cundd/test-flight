<?php

declare(strict_types=1);

namespace Cundd\TestFlight;

use Cundd\TestFlight\Exception\ResultException;

/**
 * @template T
 * @template E
 */
abstract class Result
{
    abstract public function isOk(): bool;

    /**
     * @return T
     *
     * @throws ResultException if Result is Err
     */
    abstract public function unwrap(): mixed;

    /**
     * @return E
     *
     * @throws ResultException if Result is Ok
     */
    abstract public function unwrapErr(): mixed;

    public function isErr(): bool
    {
        return !$this->isOk();
    }
}
