<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The color spaces the convert-colors stage targets. OutputIntent is the
 * document's own output intent.
 */
enum ColorSpace: string
{
    case Cmyk         = 'cmyk';
    case Rgb          = 'rgb';
    case Gray         = 'gray';
    case OutputIntent = 'output-intent';
}
