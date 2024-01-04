<?php

declare(strict_types=1);

namespace Gotenberg\Exceptions;

use RuntimeException;

use function error_get_last;

final class NativeFunctionErrored extends RuntimeException
{
    public static function createFromLastPhpError(): self
    {
        $error = error_get_last();

        if ($error === null) {
            throw new RuntimeException('No last PHP error');
        }

        return new self($error['message'], $error['type']);
    }
}
