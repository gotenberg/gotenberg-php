<?php

declare(strict_types=1);

namespace Gotenberg;

use function hrtime;

final class HrtimeIndex implements Index
{
    public function create(): string
    {
        return (string) hrtime(true);
    }
}
