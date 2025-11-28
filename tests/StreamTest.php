<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\Stream;
use PHPUnit\Framework\Attributes\Test;

use function file_exists;
use function file_put_contents;
use function unlink;

final class StreamTest extends TestCase
{
    #[Test]
    public function it_creates_a_stream_from_a_string(): void
    {
        $stream = Stream::string('my.txt', 'My content');
        $stream->getStream()->rewind();

        $this->assertSame('my.txt', $stream->getFilename());
        $this->assertSame('My content', $stream->getStream()->getContents());
    }

    #[Test]
    public function it_creates_a_stream_from_a_path(): void
    {
        // Create a temporary dummy file to ensure the test passes reliably
        $path = __DIR__ . '/dummy.txt';
        file_put_contents($path, 'Dummy content');

        try {
            $stream = Stream::path($path);

            $this->assertSame('dummy.txt', $stream->getFilename());
            $this->assertSame('Dummy content', $stream->getStream()->getContents());
        } finally {
            // Cleanup: remove the file after the test
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
