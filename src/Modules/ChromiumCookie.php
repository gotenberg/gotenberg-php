<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use JsonSerializable;

class ChromiumCookie implements JsonSerializable
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

    /** @return array<string, string|bool> */
    public function jsonSerialize(): array
    {
        $serialized = [
            'name' => $this->name,
            'value' => $this->value,
            'domain' => $this->domain,
        ];

        if ($this->path !== null) {
            $serialized['path'] = $this->path;
        }

        if ($this->secure !== null) {
            $serialized['secure'] = $this->secure;
        }

        if ($this->httpOnly !== null) {
            $serialized['httpOnly'] = $this->httpOnly;
        }

        if ($this->sameSite !== null) {
            $serialized['sameSite'] = $this->sameSite;
        }

        return $serialized;
    }
}
