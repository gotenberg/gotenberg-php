<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\FacturX;
use Gotenberg\Stream;
use PHPUnit\Framework\Attributes\Test;

final class FacturXTest extends TestCase
{
    #[Test]
    public function it_exposes_all_fields(): void
    {
        $xml     = Stream::string('factur-x.xml', 'XML content');
        $facturX = new FacturX(
            $xml,
            FacturX::CONFORMANCE_EN_16931,
            FacturX::DOCUMENT_TYPE_ORDER,
            '1.0',
        );

        $this->assertSame($xml, $facturX->xml);
        $this->assertSame('EN 16931', $facturX->conformanceLevel);
        $this->assertSame('ORDER', $facturX->documentType);
        $this->assertSame('1.0', $facturX->version);
    }

    #[Test]
    public function it_defaults_document_type_and_version(): void
    {
        $facturX = new FacturX(
            Stream::string('factur-x.xml', 'XML content'),
            FacturX::CONFORMANCE_BASIC,
        );

        $this->assertSame('BASIC', $facturX->conformanceLevel);
        $this->assertSame('INVOICE', $facturX->documentType);
        $this->assertSame('1.0', $facturX->version);
    }
}
