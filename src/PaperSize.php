<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The paper sizes of the Chromium and LibreOffice conversions.
 */
enum PaperSize: string
{
    case A3      = 'A3';
    case A4      = 'A4';
    case A5      = 'A5';
    case B4      = 'B4';
    case B5      = 'B5';
    case Letter  = 'Letter';
    case Legal   = 'Legal';
    case Tabloid = 'Tabloid';
}
