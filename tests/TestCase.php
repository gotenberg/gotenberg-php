<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\Exceptions\NativeFunctionErrored;
use Gotenberg\Test\Helpers\Constraints\FormFileConstraint;
use Gotenberg\Test\Helpers\Constraints\FormValueConstraint;
use PHPUnit\Framework\TestCase as BaseTestCase;

use function preg_replace;
use function trim;

abstract class TestCase extends BaseTestCase
{
    protected function sanitize(string $body): string
    {
        $sanitized = preg_replace('/\s\s+/', ' ', $body);
        if ($sanitized === null) {
            throw NativeFunctionErrored::createFromLastPhpError();
        }

        return trim($sanitized);
    }

    public function assertContainsFormValue(string $body, string $name, string $value, string $message = ''): void
    {
        $this->assertThat(
            $body,
            new FormValueConstraint($name, $value),
            $message,
        );
    }

    public function assertContainsFormFile(
        string $body,
        string $filename,
        string $content,
        string|null $contentType = null,
        string $fieldName = 'files',
        string $message = '',
    ): void {
        $this->assertThat(
            $body,
            new FormFileConstraint($filename, $content, $contentType, $fieldName),
            $message,
        );
    }
}
