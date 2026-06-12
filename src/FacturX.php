<?php

declare(strict_types=1);

namespace Gotenberg;

class FacturX
{
    public const CONFORMANCE_MINIMUM   = 'MINIMUM';
    public const CONFORMANCE_BASIC_WL  = 'BASIC WL';
    public const CONFORMANCE_BASIC     = 'BASIC';
    public const CONFORMANCE_EN_16931  = 'EN 16931';
    public const CONFORMANCE_EXTENDED  = 'EXTENDED';
    public const CONFORMANCE_XRECHNUNG = 'XRECHNUNG';

    public const DOCUMENT_TYPE_INVOICE        = 'INVOICE';
    public const DOCUMENT_TYPE_ORDER          = 'ORDER';
    public const DOCUMENT_TYPE_ORDER_RESPONSE = 'ORDER_RESPONSE';
    public const DOCUMENT_TYPE_ORDER_CHANGE   = 'ORDER_CHANGE';

    public function __construct(
        public readonly Stream $xml,
        public readonly string $conformanceLevel,
        public readonly string $documentType = self::DOCUMENT_TYPE_INVOICE,
        public readonly string $version = '1.0',
    ) {
    }
}
