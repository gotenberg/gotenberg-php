<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

class ChromiumExtraLinkTag
{
    private string $href;

    public function __construct(string $href)
    {
        $this->href = $href;
    }

    public function getHref(): string
    {
        return $this->href;
    }
}
