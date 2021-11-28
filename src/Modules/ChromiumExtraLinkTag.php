<?php

declare(strict_types=1);

namespace Gotenberg\Modules;

use Gotenberg\Stream;

class ChromiumExtraLinkTag
{
    private string $href;
    private ?Stream $stream;

    public static function url(string $url): self
    {
        return new self($url);
    }

    public static function stream(Stream $stream): self
    {
        return new self($stream->getFilename(), $stream);
    }

    public function __construct(string $href, ?Stream $stream = null)
    {
        $this->href   = $href;
        $this->stream = $stream;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getStream(): ?Stream
    {
        return $this->stream;
    }
}
