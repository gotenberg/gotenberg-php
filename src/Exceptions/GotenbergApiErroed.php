<?php

declare(strict_types=1);

namespace Gotenberg\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

use function assert;
use function is_string;

final class GotenbergApiErroed extends Exception
{
    /** @var array<string,string>  */
    private array $headers;

    public static function createFromResponse(ResponseInterface $response): self
    {
        $exception = new self($response->getBody()->getContents(), $response->getStatusCode());

        $exception->headers = [];
        foreach ($response->getHeaders() as $name => $values) {
            assert(is_string($name));
            $exception->headers[$name] = $response->getHeaderLine($name);
        }

        return $exception;
    }

    public function getGotenbergTrace(string $header = 'Gotenberg-Trace'): string
    {
        return $this->getHeaderLine($header);
    }

    public function getHeaderLine(string $name): string
    {
        if (! isset($this->headers[$name])) {
            return '';
        }

        return $this->headers[$name];
    }
}
