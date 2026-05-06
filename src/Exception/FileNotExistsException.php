<?php

declare(strict_types=1);

namespace Cundd\TestFlight\Exception;

final class FileNotExistsException extends FileNotReadableException
{
    protected const MESSAGE_TEMPLATE = 'File %s does not exist';
}
