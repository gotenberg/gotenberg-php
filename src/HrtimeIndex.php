<?php

declare(strict_types=1);

namespace Gotenberg;

use Gotenberg\Exceptions\NativeFunctionErrored;

use function hrtime;
use function is_numeric;

final class HrtimeIndex implements Index
{
    public function create(): string
    {
        $index = hrtime(true);
        if (! is_numeric($index)) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        return $index . '';
    }
}
