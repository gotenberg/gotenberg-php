<?php

declare(strict_types=1);

namespace Gotenberg;

use JsonSerializable;

final readonly class DownloadFrom implements JsonSerializable
{
    /**
     * @param array<string,string> $extraHttpHeaders
     * @param string|null          $filename         Names the downloaded file. Without it, Gotenberg takes the filename
     *                                               of the response's Content-Disposition header, else the end of the
     *                                               URL path when it has an extension.
     */
    public function __construct(
        public string $url,
        public array $extraHttpHeaders = [],
        public string|null $filename = null,
    ) {
    }

    /** @return array{url: string, extraHttpHeaders?: array<string,string>, filename?: string} */
    public function jsonSerialize(): array
    {
        $serialized = ['url' => $this->url];

        if ($this->extraHttpHeaders !== []) {
            $serialized['extraHttpHeaders'] = $this->extraHttpHeaders;
        }

        if ($this->filename !== null) {
            $serialized['filename'] = $this->filename;
        }

        return $serialized;
    }
}
