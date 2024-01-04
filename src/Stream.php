<?php

declare(strict_types=1);

namespace Gotenberg;

use Gotenberg\Exceptions\NativeFunctionErrored;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;

use function basename;
use function fopen;
use function fwrite;

class Stream
{
    public static function path(string $path, string|null $filename = null): self
    {
        $filename ??= basename($path);

        return new self($filename, new LazyOpenStream($path, 'r'));
    }

    public static function string(string $filename, string $str): self
    {
        $inmemory = fopen('php://memory', 'rb+');

        if ($inmemory === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        if (fwrite($inmemory, $str) === false) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        return new self($filename, Utils::streamFor($inmemory));
    }

    public function __construct(private string $filename, private StreamInterface $stream)
    {
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getStream(): StreamInterface
    {
        return $this->stream;
    }
}
