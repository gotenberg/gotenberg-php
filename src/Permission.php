<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * What a reader without the owner password of an encrypted PDF may do.
 */
enum Permission: string
{
    case Print             = 'print';
    case PrintHighQuality  = 'printHighQuality';
    case ModifyContent     = 'modifyContent';
    case ExtractText       = 'extractText';
    case ModifyAnnotations = 'modifyAnnotations';
    case FillForms         = 'fillForms';
    case Assemble          = 'assemble';
}
