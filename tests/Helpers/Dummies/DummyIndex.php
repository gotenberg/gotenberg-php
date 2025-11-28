<?php

declare(strict_types=1);

namespace Gotenberg\Test\Helpers\Dummies;

use Gotenberg\Index;

final class DummyIndex implements Index
{
    public function create(): string
    {
        return 'foo';
    }
}
