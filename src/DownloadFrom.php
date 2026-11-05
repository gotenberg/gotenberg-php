<?php

declare(strict_types=1);

namespace Gotenberg;

use JsonSerializable;

final readonly class DownloadFrom implements JsonSerializable
{
    /** @param array<string,string> $extraHttpHeaders */
    public function __construct(
        public string $url,
        public array $extraHttpHeaders = [],
    ) {
    }

    /** @return array{url: string, extraHttpHeaders?: array<string,string>} */
    public function jsonSerialize(): array
    {
        $serialized = ['url' => $this->url];

        if ($this->extraHttpHeaders !== []) {
            $serialized['extraHttpHeaders'] = $this->extraHttpHeaders;
        }

        return $serialized;
    }
}
