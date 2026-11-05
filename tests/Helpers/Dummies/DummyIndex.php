<?php

declare(strict_types=1);

namespace Gotenberg\Test\Helpers\Dummies;

use Gotenberg\Index;

final class DummyIndex implements Index
{
    private int $next = 0;

    public function create(): string
    {
        return (string) $this->next++;
    }
}
