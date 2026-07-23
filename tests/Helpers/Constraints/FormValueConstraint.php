<?php

declare(strict_types=1);

namespace Gotenberg\Test\Helpers\Constraints;

use GuzzleHttp\Psr7\DiagnosticValue;
use PHPUnit\Framework\Constraint\Constraint;

use function class_exists;
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

        $needle = 'Content-Disposition: form-data; name="' . $this->name . '"';

        // guzzlehttp/psr7:^3.0 removed the non-standard Content-Length header from multipart/form-data parts
        if (! class_exists(DiagnosticValue::class)) {
              $needle .= ' Content-Length: ' . mb_strlen($this->value);
        }

        $needle .= ' ' . $this->value;

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
