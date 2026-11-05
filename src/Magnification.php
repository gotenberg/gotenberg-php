<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The magnification of a document LibreOffice converts when it opens.
 */
enum Magnification: string
{
    case Default    = 'default';
    case FitWindow  = 'fitWindow';
    case FitWidth   = 'fitWidth';
    case FitVisible = 'fitVisible';
    case Zoom       = 'zoom';
}
