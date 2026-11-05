<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\SameSite;
use JsonSerializable;

final readonly class ChromiumCookie implements JsonSerializable
{
    public function __construct(
        public string $name,
        public string $value,
        public string $domain,
        public string|null $path = null,
        public bool|null $secure = null,
        public bool|null $httpOnly = null,
        public SameSite|null $sameSite = null,
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
            $serialized['sameSite'] = $this->sameSite->value;
        }

        return $serialized;
    }
}
