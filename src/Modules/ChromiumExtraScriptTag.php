<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\Stream;

class ChromiumExtraScriptTag
{
    private string $src;
    private ?Stream $stream;

    public static function url(string $url): self
    {
        return new self($url);
    }

    public static function stream(Stream $stream): self
    {
        return new self($stream->getFilename(), $stream);
    }

    public function __construct(string $src, ?Stream $stream = null)
    {
        $this->src    = $src;
        $this->stream = $stream;
    }

    public function getSrc(): string
    {
        return $this->src;
    }

    public function getStream(): ?Stream
    {
        return $this->stream;
    }
}
