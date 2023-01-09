<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\Index;

final class DummyIndex implements Index
{
    public function create(): string
    {
        return 'foo';
    }
}
