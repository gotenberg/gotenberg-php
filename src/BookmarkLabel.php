<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * Where merge reads the label of the bookmark it adds for each PDF.
 */
enum BookmarkLabel: string
{
    /** The PDF's title metadata. */
    case Title = 'title';

    /** The PDF's filename without the extension. */
    case Filename = 'filename';
}
