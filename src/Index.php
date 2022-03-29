<?php

declare(strict_types=1);

namespace Gotenberg;

use function chr;
use function intval;

final class Index
{
    public static function toAlpha(int $index): string
    {
        if ($index <= 0) {
            return '';
        }

        $alpha = '';

        while ($index !== 0) {
            $p     = ($index - 1) % 26;
            $index = intval(($index - $p) / 26);
            $alpha = chr(65 + $p) . $alpha;
        }

        return $alpha;
    }
}
