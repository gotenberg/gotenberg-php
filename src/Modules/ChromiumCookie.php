<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

class ChromiumCookie
{
    public function __construct(
        public readonly string $name,
        public readonly string $value,
        public readonly string $domain,
        public readonly string|null $path = null,
        public readonly bool|null $secure = null,
        public readonly bool|null $httpOnly = null,
        public readonly string|null $sameSite = null,
    ) {
    }
}
