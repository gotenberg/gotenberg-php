<?php

declare(strict_types=1);

namespace Gotenberg;

use JsonSerializable;

class DownloadFrom implements JsonSerializable
{
    /** @param array<string,string> $extraHttpHeaders */
    public function __construct(
        public readonly string $url,
        public readonly array|null $extraHttpHeaders = null,
        public readonly bool $embedded = false,
    ) {
    }

    /** @return array<string, array<string,string>|bool|string> */
    public function jsonSerialize(): array
    {
        $serialized = [
            'url' => $this->url,
            'embedded' => $this->embedded,
        ];

        if (! empty($this->extraHttpHeaders)) {
            $serialized['extraHttpHeaders'] = $this->extraHttpHeaders;
        }

        return $serialized;
    }
}
