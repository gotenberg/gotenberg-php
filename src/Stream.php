<?php

declare(strict_types=1);

namespace Gotenberg;

use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;

use function basename;

final readonly class Stream
{
    public static function path(string $path, string|null $filename = null): self
    {
        return new self($filename ?? basename($path), new LazyOpenStream($path, 'r'));
    }

    public static function string(string $filename, string $str): self
    {
        return new self($filename, Utils::streamFor($str));
    }

    public function __construct(public string $filename, public StreamInterface $stream)
    {
    }
}
