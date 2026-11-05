<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * How a viewer displays a document LibreOffice converts when it opens.
 */
enum InitialView: string
{
    case Default    = 'default';
    case Outline    = 'outline';
    case Thumbnails = 'thumbnails';
}
