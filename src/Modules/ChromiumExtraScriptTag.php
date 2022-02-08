<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

class ChromiumExtraScriptTag
{
    private string $src;

    public function __construct(string $src)
    {
        $this->src = $src;
    }

    public function getSrc(): string
    {
        return $this->src;
    }
}
