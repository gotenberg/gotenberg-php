<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The SameSite attribute of a Chromium cookie.
 */
enum SameSite: string
{
    case Strict = 'Strict';
    case Lax    = 'Lax';
    case None   = 'None';
}
