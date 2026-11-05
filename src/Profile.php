<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The standards Gotenberg remediates and validates PDFs against.
 */
enum Profile: string
{
    case PdfA1a  = 'PDF/A-1a';
    case PdfA1b  = 'PDF/A-1b';
    case PdfA2a  = 'PDF/A-2a';
    case PdfA2b  = 'PDF/A-2b';
    case PdfA2u  = 'PDF/A-2u';
    case PdfA3a  = 'PDF/A-3a';
    case PdfA3b  = 'PDF/A-3b';
    case PdfA3u  = 'PDF/A-3u';
    case PdfA4   = 'PDF/A-4';
    case PdfA4e  = 'PDF/A-4e';
    case PdfA4f  = 'PDF/A-4f';
    case PdfUa1  = 'PDF/UA-1';
    case PdfUa2  = 'PDF/UA-2';
    case FacturX = 'Factur-X';
}
