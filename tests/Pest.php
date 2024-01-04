<?php

declare(strict_types=1);

use Gotenberg\Exceptions\NativeFunctionErrored;

function sanitize(string $body): string
{
    $sanitized = preg_replace('/\s\s+/', ' ', $body);
    if ($sanitized === null) {
        throw NativeFunctionErrored::createFromLastPhpError();
    }

    return trim($sanitized);
}

expect()->extend('toContainFormValue', function (string $name, string $value) {
    $length = mb_strlen($value);

    return $this->toContain(
        'Content-Disposition: form-data; name="' . $name . '" Content-Length: ' . $length . ' ' . $value,
    );
});

expect()->extend('toContainFormFile', function (string $filename, string $content, string|null $contentType = null) {
    $length = mb_strlen($content);

    $needle =  'Content-Disposition: form-data; name="files"; filename="' . $filename . '" Content-Length: ' . $length;
    if ($contentType !== null) {
        $needle .= ' Content-Type: ' . $contentType;
    }

    return $this->toContain($needle);
});
