<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * Where the stamp stages place a stamp on each page, as a viewer shows it:
 * its center, the middle of an edge, or a corner. x and y then move the stamp
 * from there, right and up.
 */
enum StampPosition: string
{
    case Center      = 'center';
    case TopLeft     = 'top-left';
    case Top         = 'top';
    case TopRight    = 'top-right';
    case Left        = 'left';
    case Right       = 'right';
    case BottomLeft  = 'bottom-left';
    case Bottom      = 'bottom';
    case BottomRight = 'bottom-right';
}
