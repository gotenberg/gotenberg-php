<?php

declare(strict_types=1);

namespace Gotenberg;

/**
 * The algorithms the encrypt stage protects a PDF with.
 */
enum EncryptionAlgorithm: string
{
    case Aes256  = 'aes256';
    case Aes128  = 'aes128';
    case Rc4_128 = 'rc4-128';
    case Rc4_40  = 'rc4-40';
}
