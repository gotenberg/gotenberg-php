<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The formats the extract-text stage writes the text in.
 */
enum TextFormat: string
{
    case Text     = 'text';
    case Markdown = 'markdown';
}
