<?php

declare(strict_types=1);

namespace Gotenberg\Exceptions;

use Exception;
use Psr\Http\Message\ResponseInterface;

final class GotenbergApiErrored extends Exception
{
    private ResponseInterface $response;

    public static function createFromResponse(ResponseInterface $response): self
    {
        $exception           = new self($response->getBody()->getContents(), $response->getStatusCode());
        $exception->response = $response;
        $exception->response->getBody()->rewind();

        return $exception;
    }

    public function getCorrelationId(string $header = 'Gotenberg-Trace'): string
    {
        return $this->response->getHeaderLine($header);
    }

    /** @deprecated Use getCorrelationId() instead. */
    public function getGotenbergTrace(string $header = 'Gotenberg-Trace'): string
    {
        return $this->getCorrelationId($header);
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
