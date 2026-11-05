<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The Factur-X document types written in the metadata.
 */
enum FacturXDocumentType: string
{
    case Invoice       = 'INVOICE';
    case Order         = 'ORDER';
    case OrderResponse = 'ORDER_RESPONSE';
    case OrderChange   = 'ORDER_CHANGE';
}
