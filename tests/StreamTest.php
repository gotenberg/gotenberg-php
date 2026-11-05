<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\Stream;
use PHPUnit\Framework\Attributes\Test;

final class StreamTest extends TestCase
{
    #[Test]
    public function it_creates_a_stream_from_a_string(): void
    {
        $stream = Stream::string('my.txt', 'My content');

        $this->assertSame('my.txt', $stream->filename);
        $this->assertSame('My content', $stream->stream->getContents());
    }

    #[Test]
    public function it_creates_a_stream_from_a_path(): void
    {
        $path = __DIR__ . '/Helpers/Dummies/dummy.txt';

        $this->assertSame('dummy.txt', Stream::path($path)->filename);
        $this->assertSame('other.txt', Stream::path($path, 'other.txt')->filename);
        $this->assertNotSame('', Stream::path($path)->stream->getContents());
    }
}
