<?php

declare(strict_types=1);

namespace Gotenberg;

class SplitMode
{
    public function __construct(
        public readonly string $mode,
        public readonly string $span,
        public readonly bool $unify,
    ) {
    }

    public static function intervals(int $span): self
    {
        return new self(
            'intervals',
            $span . '',
            false,
        );
    }

    public static function pages(string $span, bool $unify = false): self
    {
        return new self(
            'pages',
            $span,
            $unify,
        );
    }
}
