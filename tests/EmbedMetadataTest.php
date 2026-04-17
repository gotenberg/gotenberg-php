<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\EmbedMetadata;
use PHPUnit\Framework\Attributes\Test;

use function json_encode;

final class EmbedMetadataTest extends TestCase
{
    #[Test]
    public function it_serializes_all_fields(): void
    {
        $metadata = new EmbedMetadata(
            'embed_1.xml',
            'text/xml',
            EmbedMetadata::RELATIONSHIP_DATA,
        );

        $this->assertSame(
            '{"mimeType":"text\/xml","relationship":"Data"}',
            json_encode($metadata),
        );
    }

    #[Test]
    public function it_serializes_only_mime_type(): void
    {
        $metadata = new EmbedMetadata('embed_1.xml', 'text/xml');

        $this->assertSame('{"mimeType":"text\/xml"}', json_encode($metadata));
    }

    #[Test]
    public function it_serializes_only_relationship(): void
    {
        $metadata = new EmbedMetadata(
            'embed_1.xml',
            null,
            EmbedMetadata::RELATIONSHIP_ALTERNATIVE,
        );

        $this->assertSame('{"relationship":"Alternative"}', json_encode($metadata));
    }

    #[Test]
    public function it_serializes_to_empty_when_no_optional_fields(): void
    {
        $metadata = new EmbedMetadata('embed_1.xml');

        $this->assertSame('[]', json_encode($metadata));
    }

    #[Test]
    public function it_exposes_filename_as_a_public_property(): void
    {
        $metadata = new EmbedMetadata('embed_1.xml', 'text/xml');

        $this->assertSame('embed_1.xml', $metadata->filename);
    }
}
