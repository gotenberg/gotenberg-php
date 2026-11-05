<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The image formats the render stage draws pages in.
 */
enum RenderFormat: string
{
    case Png  = 'png';
    case Jpeg = 'jpeg';
}
