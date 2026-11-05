<?php

declare(strict_types=1);

namespace Gotenberg\Test;

use Gotenberg\Test\Helpers\Constraints\FormFileConstraint;
use Gotenberg\Test\Helpers\Constraints\FormValueConstraint;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Psr\Http\Message\RequestInterface;

use function preg_replace;
use function trim;

abstract class TestCase extends BaseTestCase
{
    protected function sanitize(string $body): string
    {
        return trim((string) preg_replace('/\s\s+/', ' ', $body));
    }

    /**
     * Asserts the request targets $endpoint and carries the form values and
     * files.
     *
     * @param list<array{0: string, 1: string}>                             $values [name, value]
     * @param list<array{0: string, 1: string, 2: string, 3?: string|null}> $files  [field, filename, content, content type]
     */
    protected function assertRequest(RequestInterface $request, string $endpoint, array $values = [], array $files = []): void
    {
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('https://my.url' . $endpoint, (string) $request->getUri());

        $body = $this->sanitize($request->getBody()->getContents());

        foreach ($values as [$name, $value]) {
            $this->assertContainsFormValue($body, $name, $value);
        }

        foreach ($files as $file) {
            $this->assertContainsFormFile($body, $file[1], $file[2], $file[3] ?? null, $file[0]);
        }
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
