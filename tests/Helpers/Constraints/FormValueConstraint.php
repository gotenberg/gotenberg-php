<?php

declare(strict_types=1);

namespace Gotenberg\Test\Helpers\Constraints;

use PHPUnit\Framework\Constraint\Constraint;

use function is_string;
use function mb_strlen;
use function sprintf;
use function str_contains;

final class FormValueConstraint extends Constraint
{
    public function __construct(
        private readonly string $name,
        private readonly string $value,
    ) {
    }

    protected function matches(mixed $other): bool
    {
        if (! is_string($other)) {
            return false;
        }

        $length = mb_strlen($this->value);

        $needle = 'Content-Disposition: form-data; name="'
            . $this->name
            . '" Content-Length: '
            . $length
            . ' '
            . $this->value;

        return str_contains($other, $needle);
    }

    public function toString(): string
    {
        return sprintf(
            'contains the form value "%s" for field "%s"',
            $this->value,
            $this->name,
        );
    }
}
