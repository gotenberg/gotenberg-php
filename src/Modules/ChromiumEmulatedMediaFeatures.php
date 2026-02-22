<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use JsonSerializable;

class ChromiumEmulatedMediaFeatures implements JsonSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly string $value,
    ) {
    }

    /** @return array<string, string|bool> */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }
}
