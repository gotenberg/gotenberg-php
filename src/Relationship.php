<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The PDF/A-3 relationships of an attached file to the document.
 */
enum Relationship: string
{
    case Source      = 'Source';
    case Data        = 'Data';
    case Alternative = 'Alternative';
    case Supplement  = 'Supplement';
    case Unspecified = 'Unspecified';
}
