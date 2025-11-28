<?php

declare(strict_types=1);

namespace Gotenberg\Test\Helpers\Constraints;

use PHPUnit\Framework\Constraint\Constraint;

use function is_string;
use function mb_strlen;
use function sprintf;
use function str_contains;

final class FormFileConstraint extends Constraint
{
    public function __construct(
        private readonly string $filename,
        private readonly string $content,
        private readonly string|null $contentType = null,
        private readonly string $fieldName = 'files',
    ) {
    }

    protected function matches(mixed $other): bool
    {
        if (! is_string($other)) {
            return false;
        }

        $length = mb_strlen($this->content);

        $needle = 'Content-Disposition: form-data; name="'
            . $this->fieldName
            . '"; filename="'
            . $this->filename
            . '" Content-Length: '
            . $length;

        if ($this->contentType !== null) {
            $needle .= ' Content-Type: ' . $this->contentType;
        }

        return str_contains($other, $needle);
    }

    public function toString(): string
    {
        return sprintf(
            'contains the form file "%s" with content length %d for field "%s"',
            $this->filename,
            mb_strlen($this->content),
            $this->fieldName,
        );
    }
}
