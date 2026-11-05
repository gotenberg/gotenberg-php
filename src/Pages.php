<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The page selection keywords. A pages option also takes a page ("3"), a range
 * ("2-5") or a list of both ("1,3,5-7") as a string.
 */
enum Pages: string
{
    case All  = 'all';
    case Odd  = 'odd';
    case Even = 'even';
    case Last = 'last';
}
