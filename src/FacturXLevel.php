<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The Factur-X / ZUGFeRD conformance levels.
 */
enum FacturXLevel: string
{
    case Minimum   = 'MINIMUM';
    case BasicWl   = 'BASIC WL';
    case Basic     = 'BASIC';
    case En16931   = 'EN 16931';
    case Extended  = 'EXTENDED';
    case XRechnung = 'XRECHNUNG';
}
