<?php

declare(strict_types=1);

namespace Gotenberg;

use JsonSerializable;

class EmbedMetadata implements JsonSerializable
{
    public const FIELD_MIME_TYPE    = 'mimeType';
    public const FIELD_RELATIONSHIP = 'relationship';

    public const RELATIONSHIP_SOURCE      = 'Source';
    public const RELATIONSHIP_DATA        = 'Data';
    public const RELATIONSHIP_ALTERNATIVE = 'Alternative';
    public const RELATIONSHIP_SUPPLEMENT  = 'Supplement';
    public const RELATIONSHIP_UNSPECIFIED = 'Unspecified';

    public function __construct(
        public readonly string $filename,
        public readonly string|null $mimeType = null,
        public readonly string|null $relationship = null,
    ) {
    }

    /** @return array<string,string> */
    public function jsonSerialize(): array
    {
        $serialized = [];

        if ($this->mimeType !== null) {
            $serialized[self::FIELD_MIME_TYPE] = $this->mimeType;
        }

        if ($this->relationship !== null) {
            $serialized[self::FIELD_RELATIONSHIP] = $this->relationship;
        }

        return $serialized;
    }
}
