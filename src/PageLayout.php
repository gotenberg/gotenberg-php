<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The page layout of a document LibreOffice converts when it opens.
 */
enum PageLayout: string
{
    case Default        = 'default';
    case SinglePage     = 'singlePage';
    case OneColumn      = 'oneColumn';
    case TwoColumnRight = 'twoColumnRight';
}
